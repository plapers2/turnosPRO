<?php

namespace App\Livewire\Appointments;

use App\Mail\AppointmentCancelledByEmployeeMail;
use App\Mail\AppointmentConfirmedByEmployeeMail;
use App\Mail\AppointmentCompletedMail;
use App\Models\Appointment;
use App\Models\User;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    // -- Vista
    public string $view = 'list';

    // -- Roles
    public bool $isAdmin    = false;
    public bool $isEmployee = false;

    // -- Empresa activa
    public ?int $companyId = null;

    // -- Filtros
    public string $search             = '';
    public ?int   $filterProfessional = null;
    public string $filterStatus       = '';
    public string $filterDateFrom     = '';
    public string $filterDateTo       = '';

    // -- Modal detalle
    public bool         $showModal    = false;
    public ?Appointment $selectedAppt = null;

    // -- Modal cancelacion
    public bool    $showCancelConfirm  = false;
    public ?int    $cancelTargetId     = null;
    public string  $cancellationReason = '';

    // -- Modal confirmar
    public bool $showConfirmConfirm = false;
    public ?int $confirmTargetId   = null;

    // -- Modal completar
    public bool $showCompleteConfirm = false;
    public ?int $completeTargetId   = null;

    // -- Calendario
    public string $calendarMonth;

    public function mount(): void
    {
        $this->calendarMonth = now()->format('Y-m');
        $this->companyId     = session('active_company_id');

        $user             = auth()->user();
        $this->isAdmin    = $user->hasRole('admin');
        $this->isEmployee = $user->hasRole('empleado');
    }

    #[On('active-company-changed')]
    public function onCompanyChanged(): void
    {
        $this->companyId = session('active_company_id');
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

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

    private function baseQuery()
    {
        return $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ])
        )
            ->when(
                ! $this->isAdmin,
                fn($q) => $q->where('user_id', auth()->id())
            )
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($inner) => $inner
                        ->whereHas(
                            'customer',
                            fn($c) => $c->withTrashed()->whereHas(
                                'user',
                                fn($u) => $u->withTrashed()->where('users.name', 'like', "%{$this->search}%")
                            )
                        )
                        ->orWhereHas(
                            'user',
                            fn($u) => $u->withTrashed()->where('users.name', 'like', "%{$this->search}%")
                        )
                        ->orWhereHas(
                            'services',
                            fn($s) => $s->withTrashed()->where('services.name', 'like', "%{$this->search}%")
                        )
                )
            )
            ->when(
                $this->isAdmin && $this->filterProfessional,
                fn($q) => $q->where('user_id', $this->filterProfessional)
            )
            ->when($this->filterStatus,   fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo,   fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo));
    }

    // -- Watchers filtros
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
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedView(string $value): void
    {
        $this->dispatch('viewChanged', view: $value);
    }

    // -- Detalle
    public function viewAppointment(int $id): void
    {
        $query = $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ])
        )
            ->when(
                ! $this->isAdmin,
                fn($q) => $q->where('user_id', auth()->id())
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

    // -- Confirmacion
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

        $appointment->load([
            'customer' => fn($q) => $q->withTrashed(),
            'user'     => fn($q) => $q->withTrashed(),
            'company',
            'services' => fn($q) => $q->withTrashed(),
        ]);

        $this->enviarEmail(
            new AppointmentConfirmedByEmployeeMail($appointment),
            $appointment->customer?->email ?? '',
            $appointment->id,
            'confirmed_by_employee'
        );

        $this->showConfirmConfirm = false;
        $this->confirmTargetId   = null;

        if ($this->showModal && $this->selectedAppt?->id === $appointment->id) {
            $this->selectedAppt = $appointment->fresh([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ]);
        }

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita confirmada correctamente.');
    }

    public function openConfirmAndClose(int $id): void
    {
        $this->openConfirmModal($id);
        $this->closeModal();
    }

    // -- Cancelacion
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
            $this->dispatch('notify', type: 'error', message: 'No es posible cancelar una cita con menos de 2 horas de antelacion.');
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
            'cancellationReason.required' => 'El motivo de cancelacion es obligatorio.',
            'cancellationReason.min'      => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        if (! $this->cancelTargetId) return;

        $this->authorizeAppointmentAction($this->cancelTargetId);

        $appointment = Appointment::with([
            'customer' => fn($q) => $q->withTrashed(),
            'user'     => fn($q) => $q->withTrashed(),
            'services' => fn($q) => $q->withTrashed(),
        ])->findOrFail($this->cancelTargetId);

        $appointment->update([
            'status'              => 'cancelled',
            'cancelled_by'        => auth()->id(),
            'cancellation_reason' => $this->cancellationReason,
        ]);

        $this->enviarEmail(
            new AppointmentCancelledByEmployeeMail($appointment),
            $appointment->customer?->email ?? '',
            $appointment->id,
            'cancelled_by_employee'
        );

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

    // -- Completar
    public function openCompleteModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);

        $appointment = Appointment::findOrFail($id);

        abort_if($appointment->status !== 'confirmed', 422, 'Solo se pueden completar citas confirmadas.');
        abort_if(now()->lt($appointment->end_time), 422, 'La cita aun no ha finalizado.');

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
        abort_if(now()->lt($appointment->end_time), 422, 'La cita aun no ha finalizado.');

        $appointment->update([
            'status'       => 'completed',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        $appointment->load([
            'customer' => fn($q) => $q->withTrashed(),
            'user'     => fn($q) => $q->withTrashed(),
            'company',
            'services' => fn($q) => $q->withTrashed(),
        ]);

        $this->enviarEmail(
            new AppointmentCompletedMail($appointment),
            $appointment->customer?->email ?? '',
            $appointment->id,
            'completed'
        );

        $this->showCompleteConfirm = false;
        $this->completeTargetId   = null;

        if ($this->showModal && $this->selectedAppt?->id === $appointment->id) {
            $this->selectedAppt = $appointment->fresh([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ]);
        }

        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita marcada como completada.');
    }

    public function openCompleteAndClose(int $id): void
    {
        $this->openCompleteModal($id);
        $this->closeModal();
    }

    // -- Helpers
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
        $this->dispatch('calendarEventsUpdated', events: $this->calendarEvents);
    }

    // -- Autorizacion
    protected function authorizeAppointmentAction(int $id): void
    {
        $appt = Appointment::findOrFail($id);

        if ($this->companyId) {
            abort_if(
                ! $appt->company()->where('companies.id', $this->companyId)->exists(),
                403,
                'Esta cita no pertenece a la empresa activa.'
            );
        }

        if ($this->isAdmin) return;

        abort_if(
            $appt->user_id !== auth()->id(),
            403,
            'No tienes permiso para modificar esta cita.'
        );
    }

    // -- Computed
    #[Computed]
    public function appointments()
    {
        $now = now();

        return $this->baseQuery()
            ->orderByRaw("
                CASE
                    WHEN start_time >= ? THEN 0
                    ELSE 1
                END,
                CASE
                    WHEN start_time >= ? THEN start_time
                    ELSE NULL
                END ASC,
                CASE
                    WHEN start_time < ? THEN start_time
                    ELSE NULL
                END DESC
            ", [$now, $now, $now])
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = $this->baseQuery()
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
        if (! $this->isAdmin) return collect();

        return User::withTrashed()
            ->role('empleado')
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
                'id'    => $a->id,
                'title' => ($a->customer?->name ?? 'Cliente eliminado') . ' · ' . $a->start_time->format('H:i'),
                'start' => $a->start_time->toIso8601String(),
                'end'   => $a->end_time->toIso8601String(),
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
                    'professional' => $a->user?->name ?? 'Empleado eliminado',
                    'services'     => $a->services->pluck('name')->join(', ') ?: 'Servicio eliminado',
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
        ]);
    }

    private function enviarEmail($mailable, string $email, int $appointmentId, string $type): void
    {
        try {
            \Log::info('Intentando enviar email', ['type' => $type, 'email' => $email]);
            Mail::to($email)->send($mailable);
            \Log::info('Email enviado', ['type' => $type]);
            NotificationLog::create([
                'appointment_id'  => $appointmentId,
                'type'            => $type,
                'recipient_email' => $email,
                'status'          => 'sent',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error enviando email', ['type' => $type, 'error' => $e->getMessage()]);
            NotificationLog::create([
                'appointment_id'  => $appointmentId,
                'type'            => $type,
                'recipient_email' => $email,
                'status'          => 'error',
                'error_message'   => $e->getMessage(),
            ]);
        }
    }
}
