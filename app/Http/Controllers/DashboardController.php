<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function baseQuery()
    {
        $user      = auth()->user();
        $companyId = session('active_company_id');

        $query = Appointment::forCompany($companyId);

        if ($user->hasRole('empleado')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('cliente')) {
            return $this->clientDashboard($user);
        }

        return $this->adminDashboard($user);
    }

    // ────────────────────────────────────────────────────────────────────────────
    // DASHBOARD CLIENTE
    // ────────────────────────────────────────────────────────────────────────────
    private function clientDashboard($user)
    {
        // Un cliente puede estar registrado en varias empresas → traer todos sus customer IDs
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        if ($customerIds->isEmpty()) {
            return view('dashboard-cliente', [
                'customer'        => null,
                'stats'           => [],
                'upcoming'        => collect(),
                'history'         => collect(),
                'topServices'     => collect(),
                'nextAppointment' => null,
            ]);
        }

        // Customer "principal" para mostrar nombre/datos del perfil en la vista
        $customer = Customer::where('user_id', $user->id)->first();

        $now = now();

        // ── Citas próximas (activas/pendientes) ──
        $upcoming = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_PENDING,
            ])
            ->where('start_time', '>=', $now)
            ->with(['services', 'user'])
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id'       => $a->id,
                'date'     => $a->start_time->isoFormat('ddd D MMM'),
                'time'     => $a->start_time->format('H:i'),
                'services' => $a->services->pluck('name')->join(', '),
                'staff'    => optional($a->user)->name ?? 'Sin asignar',
                'status'   => $a->status,
                'label'    => match ($a->status) {
                    Appointment::STATUS_CONFIRMED => 'Confirmada',
                    Appointment::STATUS_PENDING   => 'Pendiente',
                    default                       => $a->status,
                },
            ]);

        // ── Historial reciente (completadas/canceladas) ──
        $history = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
            ])
            ->with(['services', 'user'])
            ->orderByDesc('start_time')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id'       => $a->id,
                'date'     => $a->start_time->isoFormat('D MMM YYYY'),
                'time'     => $a->start_time->format('H:i'),
                'services' => $a->services->pluck('name')->join(', '),
                'staff'    => optional($a->user)->name ?? 'Sin asignar',
                'status'   => $a->status,
                'label'    => match ($a->status) {
                    Appointment::STATUS_COMPLETED => 'Completada',
                    Appointment::STATUS_CANCELLED => 'Cancelada',
                    default                       => $a->status,
                },
            ]);

        // ── Totales globales del cliente ──
        $allAppointments = Appointment::whereIn('customer_id', $customerIds);

        $totalCitas       = (clone $allAppointments)->count();
        $citasCompletadas = (clone $allAppointments)->where('status', Appointment::STATUS_COMPLETED)->count();
        $citasCanceladas  = (clone $allAppointments)->where('status', Appointment::STATUS_CANCELLED)->count();
        $citasActivas     = (clone $allAppointments)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_PENDING])
            ->where('start_time', '>=', $now->copy())
            ->count();

        // ── Citas últimos 30 días (para gráfico de actividad) ──
        $last30 = Appointment::whereIn('customer_id', $customerIds)
            ->where('start_time', '>=', $now->copy()->subDays(29)->startOfDay())
            ->selectRaw("DATE(start_time) as day, COUNT(*) as total")
            ->groupByRaw("DATE(start_time)")
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $day           = $now->copy()->subDays($i)->format('Y-m-d');
            $chartLabels[] = $now->copy()->subDays($i)->format('d/m');
            $chartData[]   = (int) ($last30[$day] ?? 0);
        }

        // ── Servicios más usados por el cliente ──
        $topServices = DB::table('appointment_service')
            ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
            ->join('services', 'services.id', '=', 'appointment_service.service_id')
            ->whereIn('appointments.customer_id', $customerIds)
            ->whereNull('appointments.deleted_at')
            ->where('appointments.status', '!=', Appointment::STATUS_CANCELLED)
            ->select('services.name', DB::raw('COUNT(*) as count'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('count')
            ->limit(4)
            ->get();

        // ── Tasa de asistencia ──
        $attendedPct = $totalCitas > 0
            ? round(($citasCompletadas / $totalCitas) * 100)
            : 0;

        // ── Próxima cita (la más inmediata) ──
        $nextAppointment = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_PENDING])
            ->where('start_time', '>=', $now->copy())
            ->with('services')
            ->orderBy('start_time')
            ->first();

        $stats = [
            'total'       => $totalCitas,
            'completadas' => $citasCompletadas,
            'canceladas'  => $citasCanceladas,
            'activas'     => $citasActivas,
            'attendedPct' => $attendedPct,
            'chart'       => [
                'labels' => $chartLabels,
                'data'   => $chartData,
            ],
        ];

        return view('dashboard-cliente', compact(
            'customer',
            'stats',
            'upcoming',
            'history',
            'topServices',
            'nextAppointment'
        ));
    }

    // ────────────────────────────────────────────────────────────────────────────
    // DASHBOARD ADMIN / EMPLEADO
    // ────────────────────────────────────────────────────────────────────────────
    private function adminDashboard($user)
    {
        $companyId = session('active_company_id');

        $periods = [
            'hoy'    => [now()->startOfDay(),   now()->endOfDay()],
            'semana' => [now()->startOfWeek(),  now()->endOfWeek()],
            'mes'    => [now()->startOfMonth(), now()->endOfMonth()],
        ];

        $kpis = [];

        foreach ($periods as $key => [$start, $end]) {

            $base = $this->baseQuery()
                ->whereBetween('start_time', [$start, $end]);

            $total     = (clone $base)->count();
            $confirmed = (clone $base)->where('status', Appointment::STATUS_CONFIRMED)->count();
            $cancelled = (clone $base)->where('status', Appointment::STATUS_CANCELLED)->count();
            $completed = (clone $base)->where('status', Appointment::STATUS_COMPLETED)->count();
            $pending   = (clone $base)->where('status', Appointment::STATUS_PENDING)->count();

            $attendedPct = $total > 0 ? round(($confirmed / $total) * 100) : 0;

            $rows = (clone $base)
                ->selectRaw("DATE(start_time) as day, status, COUNT(*) as total")
                ->groupByRaw("DATE(start_time), status")
                ->get();

            // ── Generar días según el período real ──
            $days = match ($key) {
                'hoy'    => collect([now()->format('Y-m-d')]),
                'semana' => collect(range(0, 6))->map(
                    fn($i) => now()->startOfWeek()->addDays($i)->format('Y-m-d')
                ),
                'mes'    => collect(range(0, now()->daysInMonth - 1))->map(
                    fn($i) => now()->startOfMonth()->addDays($i)->format('Y-m-d')
                ),
            };

            $labels = $days->map(
                fn($d) => \Carbon\Carbon::parse($d)->isoFormat('ddd D')
            )->toArray();

            $confirmedData = $days->map(
                fn($d) => (int) $rows
                    ->where('day', $d)
                    ->where('status', Appointment::STATUS_CONFIRMED)
                    ->sum('total')
            )->toArray();

            $cancelledData = $days->map(
                fn($d) => (int) $rows
                    ->where('day', $d)
                    ->where('status', Appointment::STATUS_CANCELLED)
                    ->sum('total')
            )->toArray();

            $totalPerDay = $days->map(
                fn($d) => (int) $rows->where('day', $d)->sum('total')
            )->toArray();

            $kpis[$key] = [
                'kpis' => [
                    ['label' => 'Total',       'value' => $total,     'style' => 'primary'],
                    ['label' => 'Confirmadas', 'value' => $confirmed, 'style' => 'success'],
                    ['label' => 'Canceladas',  'value' => $cancelled, 'style' => 'error'],
                    ['label' => 'Pendientes',  'value' => $pending,   'style' => 'default'],
                ],
                'barras' => [
                    'labels'   => $labels,
                    'datasets' => [[
                        'label'           => 'Citas',
                        'data'            => $totalPerDay,
                        'backgroundColor' => '#6366f1',
                    ]],
                ],
                'lineas' => [
                    'labels'   => $labels,
                    'datasets' => [
                        [
                            'label'       => 'Confirmadas',
                            'data'        => $confirmedData,
                            'borderColor' => '#22c55e',
                            'fill'        => false,
                            'tension'     => 0.4,
                        ],
                        [
                            'label'       => 'Canceladas',
                            'data'        => $cancelledData,
                            'borderColor' => '#ef4444',
                            'borderDash'  => [5, 5],
                            'fill'        => false,
                            'tension'     => 0.4,
                        ],
                    ],
                ],
                'asistencia' => [
                    'pct'  => $attendedPct,
                    'text' => "{$confirmed} de {$total} citas confirmadas",
                ],
            ];
        }

        // ── Próximas citas (próximas 2 horas) ──
        $appointments = $this->baseQuery()
            ->with(['customer', 'services', 'user'])
            ->whereBetween('start_time', [now(), now()->addHours(2)])
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->orderBy('start_time')
            ->get()
            ->map(fn($a) => [
                'time'    => $a->start_time->format('H:i'),
                'name'    => optional($a->customer)->name ?? 'Sin cliente',
                'service' => $a->services->pluck('name')->join(', '),
                'staff'   => optional($a->user)->name ?? 'Sin asignar',
                'status'  => $a->status,
                'label'   => match ($a->status) {
                    Appointment::STATUS_CONFIRMED => 'Confirmada',
                    Appointment::STATUS_PENDING   => 'Pendiente',
                    Appointment::STATUS_COMPLETED => 'Completada',
                    Appointment::STATUS_CANCELLED => 'Cancelada',
                    default                       => $a->status,
                },
            ]);

        // ── Servicios más usados (solo admin) ──
        $services = null;

        if ($user->hasRole('admin')) {
            foreach ($periods as $key => [$start, $end]) {
                $services[$key] = DB::table('appointment_service')
                    ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
                    ->join('services', 'services.id', '=', 'appointment_service.service_id')
                    ->where('appointments.company_id', $companyId)
                    ->whereNull('appointments.deleted_at')
                    ->where('appointments.status', '!=', Appointment::STATUS_CANCELLED)
                    ->whereBetween('appointments.start_time', [$start, $end])
                    ->select(
                        'services.name',
                        DB::raw('COUNT(*) as count'),
                        DB::raw('SUM(services.price) as income')
                    )
                    ->groupBy('services.id', 'services.name')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->get()
                    ->map(fn($s) => [
                        $s->name,
                        (int) $s->count,
                        (float) $s->income,
                    ])
                    ->values();
            }
        }

        // ── dd() eliminado ──
        return view('dashboard', compact('kpis', 'appointments', 'services'));
    }
}
