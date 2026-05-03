<?php

namespace App\Livewire\Appointments;

use App\Mail\AppointmentCancelledByEmployeeMail;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    // ── Vista ────────────────────────────────────────────────
    public string $view = 'list';

    // ── Roles ────────────────────────────────────────────────
    public bool $isAdmin    = false;
    public bool $isEmployee = false;
    public bool $isCustomer = false;

    // ── Empresa activa ───────────────────────────────────────
    public ?int $companyId = null;

    // ── Filtros ──────────────────────────────────────────────
    public string $search             = '';
    public ?int   $filterProfessional = null;
    public string $filterStatus       = '';
    public string $filterDateFrom     = '';
    public string $filterDateTo       = '';

    // ── Modal detalle ────────────────────────────────────────
    public bool         $showModal    = false;
    public ?Appointment $selectedAppt = null;

    // ── Modal cancelación ────────────────────────────────────
    public bool    $showCancelConfirm  = false;
    public ?int    $cancelTargetId     = null;
    public string  $cancellationReason = '';

    // ── Modal confirmar ──────────────────────────────────────
    public bool $showConfirmConfirm = false;
    public ?int $confirmTargetId   = null;

    // ── Modal completar (RF-26) ──────────────────────────────
    public bool $showCompleteConfirm = false;
    public ?int $completeTargetId   = null;

    // ── Calendario ───────────────────────────────────────────
    public string $calendarMonth;

    public function mount(): void
    {
        $this->calendarMonth = now()->format('Y-m');
        $this->companyId     = session('active_company_id');

        $user             = auth()->user();
        $this->isAdmin    = $user->hasRole('admin');
        $this->isEmployee = $user->hasRole('empleado');
        $this->isCustomer = $user->hasRole('cliente');
    }

    // ── Escucha cambio de empresa activa ─────────────────────
    #[On('active-company-changed')]
    public function onCompanyChanged(): void
    {
        $this->companyId = session('active_company_id');
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    // ── Scope de empresa (reutilizable) ──────────────────────
    private function scopeCompany($query)
    {
        return $query->when(
            $this->companyId,
            fn($q) => $q->whereHas(
                'company',
                fn($c) => $c->where('companies.id', $this->companyId)
            )
        );
    }

    // ── Query base ───────────────────────────────────────────
    // Admin    → ve todas las citas de la empresa activa.
    // Empleado → ve solo sus propias citas dentro de la empresa activa (por user_id).
    // Cliente  → ve solo sus propias citas dentro de la empresa activa (por customer.user_id).
    // El scope se aplica aquí en el servidor; no depende de propiedades
    // públicas que el frontend podría manipular.
    private function baseQuery()
    {
        return $this->scopeCompany(
            Appointment::with(['customer', 'user', 'services'])
        )
            // Empleado → solo sus citas como profesional (server-side, seguro)
            ->when(
                ! $this->isAdmin && ! $this->isCustomer,
                fn($q) => $q->where('user_id', auth()->id())
            )
            // Cliente → solo sus citas como cliente (por customer.user_id, server-side, seguro)
            ->when(
                $this->isCustomer,
                fn($q) => $q->whereHas(
                    'customer',
                    fn($c) => $c->where('user_id', auth()->id())
                )
            )
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($q) => $q
                        ->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('user',   fn($u) => $u->where('name', 'like', "%{$this->search}%"))
                )
            )
            // El filtro por profesional solo aplica si el usuario es admin
            ->when(
                $this->isAdmin && $this->filterProfessional,
                fn($q) => $q->where('user_id', $this->filterProfessional)
            )
            ->when($this->filterStatus,   fn($q) => $q->where('status',  $this->filterStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo,   fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo));
    }

    // ── Watchers filtros ─────────────────────────────────────
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
        // El empleado y el cliente no pueden cambiar este filtro;
        // se ignora cualquier valor que el frontend intente enviar.
        if (! $this->isAdmin) {
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedView(string $value): void
    {
        $this->dispatch('viewChanged', view: $value);
    }

    // ── Detalle ──────────────────────────────────────────────
    public function viewAppointment(int $id): void
    {
        $query = $this->scopeCompany(
            Appointment::with(['customer', 'user', 'services'])
        )
            ->when(
                ! $this->isAdmin && ! $this->isCustomer,
                fn($q) => $q->where('user_id', auth()->id())
            )
            ->when(
                $this->isCustomer,
                fn($q) => $q->whereHas(
                    'customer',
                    fn($c) => $c->where('user_id', auth()->id())
                )
            );

        $this->selectedAppt = $query->findOrFail($id);
        $this->showModal    = true;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal');
        $this->js("setTimeout(() => \$wire.call('destroyModal'), 160)");
    }

    public function destroyModal(): void
    {
        $this->showModal    = false;
        $this->selectedAppt = null;
    }

    // ── Confirmación ─────────────────────────────────────────
    public function openConfirmModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);

        $appointment = Appointment::findOrFail($id);
        abort_if($appointment->status !== 'pending', 422, 'Solo se pueden confirmar citas pendientes.');

        $this->confirmTargetId    = $id;
        $this->showConfirmConfirm = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmConfirm = false;
        $this->confirmTargetId   = null;
    }

    public function confirmAppointment(): void
    {
        if (! $this->confirmTargetId) return;

        $this->authorizeAppointmentAction($this->confirmTargetId);

        $appointment = Appointment::findOrFail($this->confirmTargetId);
        abort_if($appointment->status !== 'pending', 422, 'Solo se pueden confirmar citas pendientes.');

        $appointment->update(['status' => 'confirmed', 'confirmed_by' => auth()->id()]);

        $this->showConfirmConfirm = false;
        $this->confirmTargetId   = null;

        if ($this->showModal && $this->selectedAppt?->id === $appointment->id) {
            $this->selectedAppt = $appointment->fresh(['customer', 'user', 'services']);
        }

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita confirmada correctamente.');
    }

    public function openConfirmAndClose(int $id): void
    {
        $this->openConfirmModal($id);
        $this->closeModal();
    }

    // ── Cancelación ──────────────────────────────────────────
    public function closeCancelModal(): void
    {
        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';
        $this->resetErrorBag();
    }

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

        $appointment = Appointment::with(['customer', 'user', 'services'])
            ->findOrFail($this->cancelTargetId);

        $appointment->update([
            'status'              => 'cancelled',
            'cancelled_by'        => auth()->id(),
            'cancellation_reason' => $this->cancellationReason,
        ]);

        Mail::to($appointment->customer->email)
            ->send(new AppointmentCancelledByEmployeeMail($appointment));

        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'warning', message: 'Cita cancelada.');
    }

    public function openCancelAndClose(int $id): void
    {
        $this->openCancelModal($id);
        $this->closeModal();
    }

    // ── Completar ─────────────────────────────────────────────
    public function openCompleteModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);

        $appointment = Appointment::findOrFail($id);

        abort_if($appointment->status !== 'confirmed', 422, 'Solo se pueden completar citas confirmadas.');
        abort_if(now()->lt($appointment->end_time), 422, 'La cita aún no ha finalizado.');

        $this->completeTargetId    = $id;
        $this->showCompleteConfirm = true;
    }

    public function closeCompleteModal(): void
    {
        $this->showCompleteConfirm = false;
        $this->completeTargetId   = null;
    }

    public function completeAppointment(): void
    {
        if (! $this->completeTargetId) return;

        $this->authorizeAppointmentAction($this->completeTargetId);

        $appointment = Appointment::findOrFail($this->completeTargetId);

        abort_if($appointment->status !== 'confirmed', 422, 'Solo se pueden completar citas confirmadas.');
        abort_if(now()->lt($appointment->end_time), 422, 'La cita aún no ha finalizado.');

        $appointment->update([
            'status'       => 'completed',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        $this->showCompleteConfirm = false;
        $this->completeTargetId   = null;

        if ($this->showModal && $this->selectedAppt?->id === $appointment->id) {
            $this->selectedAppt = $appointment->fresh(['customer', 'user', 'services']);
        }

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita marcada como completada.');
    }

    public function openCompleteAndClose(int $id): void
    {
        $this->openCompleteModal($id);
        $this->closeModal();
    }

    // ── Helpers ──────────────────────────────────────────────
    public function resetFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';

        if ($this->isAdmin) {
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    #[On('calendarGoToMonth')]
    public function onCalendarGoToMonth(string $month): void
    {
        $this->calendarMonth = $month;
        $this->refreshCalendarEvents();
    }

    #[On('calendarEventClicked')]
    public function onCalendarEventClicked(int|string $id): void
    {
        $this->viewAppointment($id);
    }

    protected function refreshCalendarEvents(): void
    {
        // Sin () porque es un #[Computed]
        $this->dispatch('calendarEventsUpdated', events: $this->calendarEvents);
    }

    // ── Autorización ─────────────────────────────────────────
    // Cliente  → solo puede ver sus citas, no modificarlas.
    // Empleado → solo puede modificar sus propias citas.
    // Admin    → puede modificar cualquier cita de la empresa activa.
    protected function authorizeAppointmentAction(int $id): void
    {
        // El cliente no puede cancelar, confirmar ni completar citas
        abort_if(
            $this->isCustomer,
            403,
            'Los clientes no pueden modificar citas.'
        );

        $appt = Appointment::findOrFail($id);

        // Verifica que la cita pertenezca a la empresa activa
        if ($this->companyId) {
            abort_if(
                ! $appt->company()->where('companies.id', $this->companyId)->exists(),
                403,
                'Esta cita no pertenece a la empresa activa.'
            );
        }

        // Si es admin de la empresa, puede operar cualquier cita de ella
        if ($this->isAdmin) return;

        // El empleado solo puede operar sus propias citas
        abort_if(
            $appt->user_id !== auth()->id(),
            403,
            'No tienes permiso para modificar esta cita.'
        );
    }

    // ── Computed ─────────────────────────────────────────────
    #[Computed]
    public function appointments()
    {
        return $this->baseQuery()
            ->orderByDesc('start_time')
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = $this->scopeCompany(Appointment::query())
            // Empleado → solo sus stats como profesional
            ->when(
                ! $this->isAdmin && ! $this->isCustomer,
                fn($q) => $q->where('user_id', auth()->id())
            )
            // Cliente → solo sus stats como cliente
            ->when(
                $this->isCustomer,
                fn($q) => $q->whereHas(
                    'customer',
                    fn($c) => $c->where('user_id', auth()->id())
                )
            )
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total'     => $counts->sum(),
            'pending'   => $counts->get('pending',   0),
            'confirmed' => $counts->get('confirmed', 0),
            'cancelled' => $counts->get('cancelled', 0),
            'completed' => $counts->get('completed', 0),
        ];
    }

    #[Computed]
    public function professionals()
    {
        // Solo el admin puede ver y filtrar por profesional
        if (! $this->isAdmin) return collect();

        return User::role('empleado')
            ->when(
                $this->companyId,
                fn($q) => $q->whereHas(
                    'companies',
                    fn($c) => $c->where('companies.id', $this->companyId)
                )
            )
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function calendarEvents(): array
    {
        return $this->baseQuery()
            ->get()
            ->map(fn($a) => [
                'id'              => $a->id,
                'title'           => $a->customer->name . ' · ' . $a->start_time->format('H:i'),
                'start'           => $a->start_time->toIso8601String(),
                'end'             => $a->end_time->toIso8601String(),
                'backgroundColor' => match ($a->status) {
                    'confirmed' => '#1D9E75',
                    'cancelled' => '#E24B4A',
                    'completed' => '#378ADD',
                    default     => '#BA7517',
                },
                'borderColor' => match ($a->status) {
                    'confirmed' => '#0F6E56',
                    'cancelled' => '#A32D2D',
                    'completed' => '#185FA5',
                    default     => '#854F0B',
                },
                'textColor'     => '#ffffff',
                'extendedProps' => [
                    'professional' => $a->user->name,
                    'services'     => $a->services->pluck('name')->join(', '),
                    'status'       => $a->status,
                ],
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.appointments.⚡manager', [
            'appointments'   => $this->appointments(),
            'stats'          => $this->stats(),
            'professionals'  => $this->professionals(),
            'calendarEvents' => $this->calendarEvents(),
            'isAdmin'        => $this->isAdmin,
            'isCustomer'     => $this->isCustomer,
        ]);
    }
}
