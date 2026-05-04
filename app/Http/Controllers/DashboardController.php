<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function baseQuery()
    {
        $user = auth()->user();
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
        $companyId = session('active_company_id');

        $periods = [
            'hoy'    => [now()->startOfDay(), now()->endOfDay()],
            'semana' => [now()->startOfWeek(), now()->endOfWeek()],
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

            // ── Agrupación por día (para charts) ──
            $rows = (clone $base)
                ->selectRaw("DATE(start_time) as day, status, COUNT(*) as total")
                ->groupByRaw("DATE(start_time), status")
                ->get();

            $days = collect(range(0, 6))->map(fn($i) => now()->startOfWeek()->addDays($i)->format('Y-m-d'));

            $labels = $days->map(fn($d) => \Carbon\Carbon::parse($d)->isoFormat('ddd'))->toArray();

            $confirmedData = $days->map(
                fn($d) =>
                (int) $rows->where('day', $d)
                    ->where('status', Appointment::STATUS_CONFIRMED)
                    ->sum('total')
            )->toArray();

            $cancelledData = $days->map(
                fn($d) =>
                (int) $rows->where('day', $d)
                    ->where('status', Appointment::STATUS_CANCELLED)
                    ->sum('total')
            )->toArray();

            $totalPerDay = $days->map(
                fn($d) =>
                (int) $rows->where('day', $d)->sum('total')
            )->toArray();

            // ── ESTRUCTURA FINAL QUE TU JS NECESITA ──
            $kpis[$key] = [

                'kpis' => [
                    [
                        'label' => 'Total',
                        'value' => $total,
                        'style' => 'primary'
                    ],
                    [
                        'label' => 'Confirmadas',
                        'value' => $confirmed,
                        'style' => 'success'
                    ],
                    [
                        'label' => 'Canceladas',
                        'value' => $cancelled,
                        'style' => 'error'
                    ],
                    [
                        'label' => 'Pendientes',
                        'value' => $pending,
                        'style' => 'default'
                    ],
                ],

                'barras' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Citas',
                            'data' => $totalPerDay,
                            'backgroundColor' => '#6366f1'
                        ]
                    ]
                ],

                'lineas' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Confirmadas',
                            'data' => $confirmedData,
                            'borderColor' => '#22c55e',
                            'fill' => false,
                            'tension' => 0.4
                        ],
                        [
                            'label' => 'Canceladas',
                            'data' => $cancelledData,
                            'borderColor' => '#ef4444',
                            'borderDash' => [5, 5],
                            'fill' => false,
                            'tension' => 0.4
                        ]
                    ]
                ],

                'asistencia' => [
                    'pct'  => $attendedPct,
                    'text' => "{$confirmed} de {$total} citas confirmadas"
                ]
                
            ];
        }

        // ── Próximas citas ──
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

        // ── Servicios (solo admin) ──
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
                        (float) $s->income
                    ])
                    ->values();
            }
        }

        return view('dashboard', compact(
            'kpis',
            'appointments',
            'services'
        ));
    }
}
