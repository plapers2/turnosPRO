<?php

namespace App\Livewire\Appointments;

use App\Mail\AppointmentConfirmedByEmployeeMail;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    // ── Vista ────────────────────────────────────────────────
    public string $view = 'list';

    // ── Rol ─────────────────────────────────────────────────
    public bool $isAdmin = false;

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

    // ── Calendario: solo necesitamos el mes para la query inicial
    //    La navegación mes/semana/día la maneja FullCalendar en el cliente.
    public string $calendarMonth;

    public function mount(): void
    {
        $this->calendarMonth = now()->format('Y-m');

        $user = auth()->user();
        $this->isAdmin = $user->hasRole('admin');

        if (! $this->isAdmin) {
            $this->filterProfessional = $user->id;
        }
    }

    // ── Query base reutilizable con todos los filtros ────────
    private function baseQuery()
    {
        return Appointment::with(['customer', 'user', 'services'])
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($q) => $q
                        ->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('user',   fn($u) => $u->where('name', 'like', "%{$this->search}%"))
                )
            )
            ->when($this->filterProfessional, fn($q) => $q->where('user_id', $this->filterProfessional))
            ->when($this->filterStatus,       fn($q) => $q->where('status',  $this->filterStatus))
            ->when($this->filterDateFrom,     fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo,       fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo));
    }

    // ── Reset paginación + refresco calendario al filtrar ────
    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }
    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }
    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterProfessional(): void
    {
        if (! $this->isAdmin) {
            $this->filterProfessional = auth()->id();
        }
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    // ── Cambio de vista ──────────────────────────────────────
    public function updatedView(string $value): void
    {
        $this->dispatch('viewChanged', view: $value);
    }

    // ── Detalle de cita ──────────────────────────────────────
    public function viewAppointment(int $id): void
    {
        $query = Appointment::with(['customer', 'user', 'services'])
            ->when(! $this->isAdmin, fn($q) => $q->where('user_id', auth()->id()));

        $this->selectedAppt = $query->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal');
        $this->js("setTimeout(() => \$wire.call('destroyModal'), 160)");
    }

    public function closeCancelModal(): void
    {
        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';
        $this->resetErrorBag();
    }

    public function destroyModal(): void
    {
        $this->showModal    = false;
        $this->selectedAppt = null;
    }

    // ── Confirmar cita ───────────────────────────────────────
    public function confirmAppointment(int $id): void
    {
        $this->authorizeAppointmentAction($id);
        $appointment = Appointment::with(['customer', 'user', 'services'])->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);

        Mail::to($appointment->customer->email)
            ->send(new AppointmentConfirmedByEmployeeMail($appointment));
        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita confirmada correctamente, se ha enviado un email al cliente.');
    }

    // ── Abrir modal de cancelación ───────────────────────────
    public function openCancelModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);

        $appointment = Appointment::findOrFail($id);

        if ($appointment->start_time->diffInMinutes(now(), false) > -120) {
            $this->dispatch('notify', type: 'error', message: 'No es posible cancelar una cita con menos de 2 horas de antelación.');
            return;
        }

        $this->cancelTargetId     = $id;
        $this->cancellationReason = '';
        $this->resetErrorBag();
        $this->showCancelConfirm  = true;
    }

    // ── Ejecutar cancelación ─────────────────────────────────
    public function cancelAppointment(): void
    {
        $this->validate([
            'cancellationReason' => 'required|min:5',
        ], [
            'cancellationReason.required' => 'El motivo de cancelación es obligatorio.',
            'cancellationReason.min'      => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        if (! $this->cancelTargetId) return;

        $this->authorizeAppointmentAction($this->cancelTargetId);

        Appointment::findOrFail($this->cancelTargetId)->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $this->cancellationReason,
        ]);

        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'warning', message: 'Cita cancelada.');
    }

    // ── Reset de filtros ─────────────────────────────────────
    public function resetFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';

        // Admin puede limpiar el filtro de profesional.
        // Empleado siempre mantiene el suyo forzado.
        if ($this->isAdmin) {
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    // ── Navegación de mes (solo desde vista mes del nav) ─────
    public function goToMonth(string $month): void
    {
        $this->calendarMonth = $month;
        $this->refreshCalendarEvents();
    }

    // ── Helper: refrescar eventos del calendario ─────────────
    protected function refreshCalendarEvents(): void
    {
        $this->dispatch('calendarEventsUpdated', events: $this->calendarEvents());
    }

    // ── Helper: autorizar acción sobre cita ──────────────────
    protected function authorizeAppointmentAction(int $id): void
    {
        if ($this->isAdmin) return;

        $appt = Appointment::findOrFail($id);

        abort_if(
            $appt->user_id !== auth()->id(),
            403,
            'No tienes permiso para modificar esta cita.'
        );
    }

    // ── Computed: citas paginadas ────────────────────────────
    #[Computed]
    public function appointments()
    {
        return $this->baseQuery()
            ->orderByDesc('start_time')
            ->paginate(15);
    }

    // ── Computed: stats ──────────────────────────────────────
    #[Computed]
    public function stats(): array
    {
        $counts = Appointment::query()
            ->when(! $this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
            ->selectRaw('status, count(*) as total')
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
    #[Computed]
    public function professionals()
    {
        if (! $this->isAdmin) return collect();

        return User::role('empleado')->orderBy('name')->get();
    }

    // ── Computed: eventos del calendario ────────────────────
    // Para el calendario global cargamos TODOS los eventos que pasen
    // los filtros activos, sin limitar al mes (FullCalendar maneja
    // la navegación en el cliente con las 3 vistas).
    #[Computed]
    public function calendarEvents(): array
    {
        return $this->baseQuery()
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,

                // Título visible en el evento
                'title' => $a->customer->name . ' · ' . $a->start_time->format('H:i'),

                'start' => $a->start_time->toIso8601String(),
                'end'   => $a->end_time->toIso8601String(),

                // Color de fondo según estado
                'backgroundColor' => match ($a->status) {
                    'confirmed' => '#1D9E75',
                    'cancelled' => '#E24B4A',
                    'completed' => '#378ADD',
                    default     => '#BA7517',   // pending
                },
                'borderColor' => match ($a->status) {
                    'confirmed' => '#0F6E56',
                    'cancelled' => '#A32D2D',
                    'completed' => '#185FA5',
                    default     => '#854F0B',
                },
                'textColor' => '#ffffff',

                // Datos extra para el eventContent de semana/día
                'extendedProps' => [
                    'professional' => $a->user->name,
                    'services'     => $a->services->pluck('name')->join(', '),
                    'status'       => $a->status,
                ],
            ])
            ->toArray();
    }

    public function confirmAndClose(int $id): void
    {
        $this->confirmAppointment($id);
        $this->closeModal();
    }

    public function openCancelAndClose(int $id): void
    {
        $this->openCancelModal($id);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.appointments.⚡manager', [
            'appointments'   => $this->appointments(),
            'stats'          => $this->stats(),
            'professionals'  => $this->professionals(),
            'calendarEvents' => $this->calendarEvents(),
            'isAdmin'        => $this->isAdmin,
        ]);
    }
}
