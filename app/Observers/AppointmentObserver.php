<?php

namespace App\Observers;

use App\Events\AppointmentUpdated;
use App\Models\Appointment;
use App\Models\AppointmentStatusLog;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        AppointmentStatusLog::create([
            'appointment_id' => $appointment->id,
            'changed_by'     => auth()->id(),
            'from_status'    => null,
            'to_status'      => $appointment->status,
            'reason'         => null,
        ]);

        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    public function updated(Appointment $appointment): void
    {
        if ($appointment->wasChanged('status')) {
            AppointmentStatusLog::create([
                'appointment_id' => $appointment->id,
                'changed_by'     => auth()->id(),
                'from_status'    => $appointment->getOriginal('status'),
                'to_status'      => $appointment->status,
                'reason'         => $appointment->cancellation_reason ?? null,
            ]);
        }

        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    public function deleted(Appointment $appointment): void
    {
        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }
}
