<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use App\Models\User;

trait HasProfessionalReassignment
{
    public ?int $reasignarAppointmentId = null;
    public array $profesionalesDisponibles = [];
    public ?int $nuevoProfesionalId = null;
    public bool $loadingProfesionales = false;
    public string $reasignarRazon = '';

    /**
     * Fuerza el cast a int cuando Livewire recibe el valor del radio button (llega como string).
     */
    public function updatedNuevoProfesionalId($value): void
    {
        $this->nuevoProfesionalId = $value !== null && $value !== '' ? (int) $value : null;
    }

    public function cargarProfesionalesReasignar(int $appointmentId): void
    {
        $this->reasignarAppointmentId = $appointmentId;
        $this->nuevoProfesionalId     = null;
        $this->reasignarRazon         = '';
        $this->loadingProfesionales   = true;
        $this->profesionalesDisponibles = [];

        $appointment = Appointment::with(['services'])->findOrFail($appointmentId);

        $serviceId  = $appointment->services()->first()?->id;
        $companyId  = $appointment->company_id;
        $inicio     = $appointment->start_time;
        $fin        = $appointment->end_time;
        $dayOfWeek  = $inicio->format('l');

        $this->profesionalesDisponibles = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereHas('services', fn($q) =>
            $q->where('services.id', $serviceId))
            ->whereHas('roles', fn($q) =>
            $q->where('name', 'empleado'))
            ->whereHas('professionalAvailabilities', fn($q) =>
            $q->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $inicio->format('H:i:s'))
                ->where('end_time',   '>=', $fin->format('H:i:s')))
            ->whereDoesntHave('appointments', fn($q) =>
            $q->where('id', '<>', $appointmentId)
                ->where('start_time', '<', $fin)
                ->where('end_time',   '>', $inicio)
                ->where('status', '<>', 'cancelled'))
            ->get(['id', 'name', 'email', 'image'])
            ->toArray();

        $this->loadingProfesionales = false;
    }

    public function confirmarReasignacion(): void
    {
        $this->validate([
            'nuevoProfesionalId' => 'required|exists:users,id',
            'reasignarRazon'     => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($this->reasignarAppointmentId);

        // Verificar conflicto en tiempo real (race condition)
        $conflicto = Appointment::where('user_id', $this->nuevoProfesionalId)
            ->where('id', '<>', $appointment->id)
            ->where('start_time', '<', $appointment->end_time)
            ->where('end_time',   '>', $appointment->start_time)
            ->where('status', '<>', 'cancelled')
            ->exists();

        if ($conflicto) {
            $this->addError('nuevoProfesionalId', 'Este profesional ya tiene una cita en ese horario.');
            return;
        }

        $appointment->update(['user_id' => $this->nuevoProfesionalId]);

        AppointmentStatusLog::create([
            'appointment_id' => $appointment->id,
            'changed_by'     => auth()->id(),
            'from_status'    => $appointment->status,
            'to_status'      => $appointment->status,
            'reason'         => 'Profesional reasignado. ' . $this->reasignarRazon,
        ]);

        // Refrescar el appt en el modal de detalle si el trait lo tiene
        if (property_exists($this, 'appt')) {
            $this->appt = $appointment->fresh(['customer', 'user', 'company', 'services', 'statusLogs']);
        }

        $this->resetReasignacion();
        $this->dispatch('notify', type: 'success', message: 'Profesional reasignado correctamente.');
    }

    public function cancelarReasignacion(): void
    {
        $this->resetReasignacion();
    }

    private function resetReasignacion(): void
    {
        $this->reasignarAppointmentId   = null;
        $this->profesionalesDisponibles = [];
        $this->nuevoProfesionalId       = null;
        $this->reasignarRazon           = '';
        $this->loadingProfesionales     = false;
    }
}
