<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;
use App\Services\AppointmentNotifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

trait HasAppointmentActions
{
    // -- Modal confirmar
    public bool $showConfirmConfirm = false;
    public ?int $confirmTargetId    = null;

    // -- Modal cancelar
    public bool   $showCancelConfirm  = false;
    public ?int   $cancelTargetId     = null;
    public string $cancellationReason = '';

    // -- Modal completar
    public bool $showCompleteConfirm = false;
    public ?int $completeTargetId    = null;
    public bool $hasAppointmentsForConfirmed = true;
    public array $appointmentForConfirmed = [];

    // -- Confirmar
    public function openConfirmModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);
        abort_if(Appointment::findOrFail($id)->status !== 'pending', 422, 'Solo se pueden confirmar citas pendientes.');

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
        $appointment->load(['customer' => fn($q) => $q->withTrashed(), 'user' => fn($q) => $q->withTrashed(), 'company', 'services' => fn($q) => $q->withTrashed()]);

        app(AppointmentNotifier::class)->send('confirmed_by_employee', $appointment);

        $this->closeConfirmModal();
        $this->refreshSelectedAppt($appointment);
        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita confirmada correctamente.');
    }

    public function openConfirmAndClose(int $id): void
    {
        $this->openConfirmModal($id);
        $this->closeModal();
    }

    // -- Cancelar
    public function openCancelModal(int $id): void
    {
        $this->authorizeAppointmentAction($id);

        $appointment = Appointment::findOrFail($id);
        $appointmentOutTime = Appointment::where('id', $id)->where('start_time', '<', now())->exists();

        if ($appointment->start_time->diffInMinutes(now(), false) > -120 && !$appointmentOutTime) {
            $this->dispatch('notify', type: 'error', message: 'No es posible cancelar una cita con menos de 2 horas de antelacion.');
            return;
        }

        $this->cancelTargetId     = $id;
        $this->cancellationReason = '';
        $this->resetErrorBag();
        $this->showCancelConfirm  = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelConfirm  = false;
        $this->cancelTargetId     = null;
        $this->cancellationReason = '';
        $this->resetErrorBag();
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

        app(AppointmentNotifier::class)->send('cancelled_by_employee', $appointment);

        $this->closeCancelModal();
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

        $appointment->load(['customer' => fn($q) => $q->withTrashed(), 'user' => fn($q) => $q->withTrashed(), 'company', 'services' => fn($q) => $q->withTrashed()]);

        app(AppointmentNotifier::class)->send('completed', $appointment);

        $this->closeCompleteModal();
        $this->refreshSelectedAppt($appointment);
        $this->refreshCalendarEvents();
        $this->dispatch('notify', type: 'success', message: 'Cita marcada como completada.');
    }

    public function openCompleteAndClose(int $id): void
    {
        $this->openCompleteModal($id);
        $this->closeModal();
    }
}
