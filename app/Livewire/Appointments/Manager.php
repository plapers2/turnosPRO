<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    // ── Vista ────────────────────────────────────────────────
    public string $view = 'list';

    // ── Filtros ──────────────────────────────────────────────
    public string $search = '';
    public ?int $filterProfessional = null;
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    // ── Modal detalle ────────────────────────────────────────
    public bool $showModal = false;
    public ?Appointment $selectedAppt = null;

    // ── Modal cancelación ────────────────────────────────────
    public bool $showCancelConfirm = false;
    public ?int $cancelTargetId = null;
    public string $cancellationReason = '';

    // ── Calendario ───────────────────────────────────────────
    public string $calendarMonth;

    // ── Listeners ────────────────────────────────────────────
    protected $listeners = [];

    // ── Emits ────────────────────────────────────────────────
    // viewChanged         → persiste vista en localStorage (JS)
    // calendarEventsUpdated → refresca FullCalendar (JS)
    // notify              → toast de feedback (Alpine)

    public function mount(): void
    {
        $this->calendarMonth = now()->format('Y-m');
    }

    // ── Reset paginación cuando cambian filtros ──────────────
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterProfessional(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }
    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
    }
    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
    }

    // ── Cambio de vista ──────────────────────────────────────
    public function updatedView(string $value): void
    {
        $this->dispatch('viewChanged', view: $value);
    }

    // ── Detalle de cita ──────────────────────────────────────
    public function viewAppointment(int $id): void
    {
        $this->selectedAppt = Appointment::with(['customer', 'user', 'services'])->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal   = false;
        $this->selectedAppt = null;
    }

    // ── Confirmar cita ───────────────────────────────────────
    public function confirmAppointment(int $id): void
    {
        $appt = Appointment::findOrFail($id);
        $appt->update(['status' => 'confirmed']);

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita confirmada correctamente.');
    }

    // ── Abrir modal de cancelación ───────────────────────────
    public function openCancelModal(int $id): void
    {
        $this->cancelTargetId      = $id;
        $this->cancellationReason  = '';
        $this->showCancelConfirm   = true;
    }

    // ── Ejecutar cancelación ─────────────────────────────────
    public function cancelAppointment(): void
    {
        if (! $this->cancelTargetId) return;

        Appointment::findOrFail($this->cancelTargetId)->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $this->cancellationReason ?: null,
        ]);

        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'warning', message: 'Cita cancelada.');
    }

    // ── Navegación de mes ────────────────────────────────────
    public function previousMonth(): void
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth . '-01')
            ->subMonth()->format('Y-m');
        $this->refreshCalendarEvents();
    }

    public function nextMonth(): void
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth . '-01')
            ->addMonth()->format('Y-m');
        $this->refreshCalendarEvents();
    }

    // ── Helpers ──────────────────────────────────────────────
    protected function refreshCalendarEvents(): void
    {
        $this->dispatch('calendarEventsUpdated', events: $this->calendarEvents());
    }

    // ── Computed: citas paginadas ────────────────────────────
    public function getAppointmentsProperty()
    {
        return Appointment::with(['customer', 'user', 'services'])
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('user',   fn($u) => $u->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->filterProfessional, fn($q) => $q->where('user_id', $this->filterProfessional))
            ->when($this->filterStatus,       fn($q) => $q->where('status',  $this->filterStatus))
            ->when($this->filterDateFrom,     fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo,       fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo))
            ->orderByDesc('start_time')
            ->paginate(15);
    }

    // ── Computed: stats ──────────────────────────────────────
    public function getStatsProperty(): array
    {
        $counts = Appointment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total'     => $counts->sum(),
            'pending'   => $counts->get('pending',   0),
            'confirmed' => $counts->get('confirmed', 0),
            'cancelled' => $counts->get('cancelled', 0),
        ];
    }

    // ── Computed: profesionales ──────────────────────────────
    public function getProfessionalsProperty()
    {
        return User::role('empleado')->orderBy('name')->get();
    }

    // ── Computed: eventos del calendario ────────────────────
    public function getCalendarEventsProperty(): array
    {
        $start = Carbon::parse($this->calendarMonth . '-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Appointment::with('customer')
            ->whereBetween('start_time', [$start, $end])
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'title' => $a->customer->name . ' ' . $a->start_time->format('H:i'),
                'start' => $a->start_time->toIso8601String(),
                'end'   => $a->end_time->toIso8601String(),
                'color' => match ($a->status) {
                    'confirmed' => '#1D9E75',
                    'cancelled' => '#E24B4A',
                    'completed' => '#378ADD',
                    default     => '#BA7517',
                },
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.appointments.⚡manager', [
            'appointments'   => $this->appointments,
            'stats'          => $this->stats,
            'professionals'  => $this->professionals,
            'calendarEvents' => $this->calendarEvents,
        ]);
    }
}
