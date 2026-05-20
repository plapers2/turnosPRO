<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class ClienteDashboard extends Component
{
    public function render()
    {
        $user        = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        $customer    = Customer::where('user_id', $user->id)->first();
        $now         = now();

        if ($customerIds->isEmpty()) {
            return view('livewire.dashboard.cliente-dashboard', [
                'customer'        => null,
                'stats'           => [],
                'upcoming'        => collect(),
                'history'         => collect(),
                'topServices'     => collect(),
                'nextAppointment' => null,
            ]);
        }

        // ── Citas próximas ──
        $upcoming = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED])
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
                    default                       => $a->status,
                },
            ]);

        // ── Historial ──
        $history = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED])
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

        // ── Totales ──
        $all            = Appointment::whereIn('customer_id', $customerIds);
        $totalCitas     = (clone $all)->count();
        $completadas    = (clone $all)->where('status', Appointment::STATUS_COMPLETED)->count();
        $canceladas     = (clone $all)->where('status', Appointment::STATUS_CANCELLED)->count();
        $activas        = (clone $all)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED])
            ->where('start_time', '>=', $now)
            ->count();

        // ── Gráfico últimos 30 días ──
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

        // ── Top servicios ──
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

        // ── Próxima cita ──
        $nextAppointment = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED])
            ->where('start_time', '>=', $now)
            ->with('services')
            ->orderBy('start_time')
            ->first();

        $stats = [
            'total'       => $totalCitas,
            'completadas' => $completadas,
            'canceladas'  => $canceladas,
            'activas'     => $activas,
            'attendedPct' => $totalCitas > 0 ? round(($completadas / $totalCitas) * 100) : 0,
            'chart'       => ['labels' => $chartLabels, 'data' => $chartData],
        ];

        return view('livewire.dashboard.cliente-dashboard', compact(
            'customer',
            'stats',
            'upcoming',
            'history',
            'topServices',
            'nextAppointment'
        ));
    }
}
