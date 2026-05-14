<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait HasDashboardData
{
    // ─────────────────────────────────────────────────────────
    // QUERY BASE — EmpleadoDashboard sobreescribe este método
    // ─────────────────────────────────────────────────────────
    protected function baseQuery()
    {
        $companyId = session('active_company_id')
            ?? auth()->user()->companies()->first()?->id;

        abort_if(is_null($companyId), 403, 'No tienes una empresa asignada.');

        return Appointment::forCompany($companyId);
    }

    // ─────────────────────────────────────────────────────────
    // RANGO DE FECHAS SEGÚN PERÍODO ACTIVO
    // ─────────────────────────────────────────────────────────
    protected function periodRange(): array
    {
        return match ($this->period) {
            'semana' => [now()->startOfWeek(),  now()->endOfWeek()],
            'mes'    => [now()->startOfMonth(), now()->endOfMonth()],
            default  => [now()->startOfDay(),   now()->endOfDay()],
        };
    }

    // ─────────────────────────────────────────────────────────
    // CACHE KEY único por empresa + usuario + período
    // ─────────────────────────────────────────────────────────
    protected function cacheKey(string $suffix): string
    {
        $companyId = session('active_company_id')
            ?? auth()->user()->companies()->first()?->id;

        return "dashboard_{$suffix}_{$companyId}_{$this->period}_" . auth()->id();
    }

    // ─────────────────────────────────────────────────────────
    // KPIs — 1 sola query con selectRaw + cache 2 min
    // ─────────────────────────────────────────────────────────
    protected function buildKpis(): array
    {
        return cache()->remember($this->cacheKey('kpis'), now()->addMinutes(2), function () {
            [$start, $end] = $this->periodRange();

            $stats = (clone $this->baseQuery())
                ->whereBetween('start_time', [$start, $end])
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(status = ?) as confirmed,
                    SUM(status = ?) as cancelled,
                    SUM(status = ?) as pending,
                    SUM(status = ?) as completed
                ", [
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_CANCELLED,
                    Appointment::STATUS_PENDING,
                    Appointment::STATUS_COMPLETED,
                ])
                ->first();

            $total     = (int) $stats->total;
            $completed = (int) $stats->completed;

            return [
                'cards' => [
                    ['label' => 'Total',       'value' => $total],
                    ['label' => 'Confirmadas', 'value' => (int) $stats->confirmed],
                    ['label' => 'Canceladas',  'value' => (int) $stats->cancelled],
                    ['label' => 'Pendientes',  'value' => (int) $stats->pending],
                ],
                'asistencia' => [
                    'pct'  => $total > 0 ? round(($completed / $total) * 100) : 0,
                    'text' => "{$completed} de {$total} citas completadas",
                ],
            ];
        });
    }

    // ─────────────────────────────────────────────────────────
    // DATOS DEL GRÁFICO — cache 2 min
    // ─────────────────────────────────────────────────────────
    protected function buildChartData(): array
    {
        return cache()->remember($this->cacheKey('chart'), now()->addMinutes(2), function () {
            [$start, $end] = $this->periodRange();

            $rows = $this->baseQuery()
                ->whereBetween('start_time', [$start, $end])
                ->selectRaw("DATE(start_time) as day, status, COUNT(*) as total")
                ->groupByRaw("DATE(start_time), status")
                ->get();

            $days = match ($this->period) {
                'semana' => collect(range(0, 6))->map(
                    fn($i) => now()->startOfWeek()->addDays($i)->format('Y-m-d')
                ),
                'mes'    => collect(range(0, now()->daysInMonth - 1))->map(
                    fn($i) => now()->startOfMonth()->addDays($i)->format('Y-m-d')
                ),
                default  => collect([now()->format('Y-m-d')]),
            };

            $labels = $days->map(
                fn($d) => Carbon::parse($d)->isoFormat('ddd D')
            )->toArray();

            $totalPerDay = $days->map(
                fn($d) => (int) $rows->where('day', $d)->sum('total')
            )->toArray();

            $confirmedData = $days->map(
                fn($d) => (int) $rows->where('day', $d)->where('status', Appointment::STATUS_CONFIRMED)->sum('total')
            )->toArray();

            $cancelledData = $days->map(
                fn($d) => (int) $rows->where('day', $d)->where('status', Appointment::STATUS_CANCELLED)->sum('total')
            )->toArray();

            return [
                'barras' => [
                    'labels'   => $labels,
                    'datasets' => [[
                        'label'           => 'Citas',
                        'data'            => $totalPerDay,
                        'backgroundColor' => '#663a00',
                        'borderRadius'    => 8,
                    ]],
                ],
                'lineas' => [
                    'labels'   => $labels,
                    'datasets' => [
                        [
                            'label'       => 'Confirmadas',
                            'data'        => $confirmedData,
                            'borderColor' => '#046289',
                            'tension'     => 0.4,
                            'fill'        => false,
                            'pointRadius' => 3,
                        ],
                        [
                            'label'       => 'Canceladas',
                            'data'        => $cancelledData,
                            'borderColor' => '#ba1a1a',
                            'tension'     => 0.4,
                            'fill'        => false,
                            'pointRadius' => 3,
                        ],
                    ],
                ],
            ];
        });
    }

    // ─────────────────────────────────────────────────────────
    // PRÓXIMAS CITAS — no depende del período, sin cache
    // ─────────────────────────────────────────────────────────
    protected function buildAppointments(): array
    {
        return $this->baseQuery()
            ->with(['customer', 'services', 'user'])
            ->where('start_time', '>=', now())
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->orderBy('start_time')
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'time'    => $a->start_time->format('H:i'),
                'date'    => $a->start_time->isoFormat('ddd D MMM'),
                'name'    => optional($a->customer)->name ?? 'Sin cliente',
                'service' => $a->services->pluck('name')->join(', '),
                'staff'   => optional($a->user)->name ?? 'Sin asignar',
                'status'  => $a->status,
                'label'   => match ($a->status) {
                    Appointment::STATUS_CONFIRMED => 'Confirmada',
                    Appointment::STATUS_PENDING   => 'Pendiente',
                    Appointment::STATUS_COMPLETED => 'Completada',
                    default                       => $a->status,
                },
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────
    // SERVICIOS MÁS SOLICITADOS — cache 2 min
    // ─────────────────────────────────────────────────────────
    protected function buildServices(): array
    {
        return cache()->remember($this->cacheKey('services'), now()->addMinutes(2), function () {
            [$start, $end] = $this->periodRange();

            $companyId = session('active_company_id')
                ?? auth()->user()->companies()->first()?->id;

            $query = DB::table('appointment_service')
                ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
                ->join('services', 'services.id', '=', 'appointment_service.service_id')
                ->where('appointments.company_id', $companyId)
                ->whereNull('appointments.deleted_at')
                ->where('appointments.status', '!=', Appointment::STATUS_CANCELLED)
                ->whereBetween('appointments.start_time', [$start, $end]);

            if ($this->applyUserFilter()) {
                $query->where('appointments.user_id', auth()->id());
            }

            return $query
                ->select('services.name', DB::raw('COUNT(*) as count'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->map(fn($s) => ['name' => $s->name, 'count' => (int) $s->count])
                ->toArray();
        });
    }

    // ─────────────────────────────────────────────────────────
    // Hook: Admin → false, Empleado → true
    // ─────────────────────────────────────────────────────────
    protected function applyUserFilter(): bool
    {
        return false;
    }
}
