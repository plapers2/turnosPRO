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

        $this->bumpDashboardCache($appointment);

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

        $this->bumpDashboardCache($appointment);

        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    public function deleted(Appointment $appointment): void
    {
        $this->bumpDashboardCache($appointment);

        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    protected function bumpDashboardCache(Appointment $appointment): void
    {
        if (! $appointment->company_id) {
            return;
        }

        cache()->forever(
            "dashboard_version_{$appointment->company_id}",
            (int) cache()->get("dashboard_version_{$appointment->company_id}", 1) + 1
        );

        cache()->forever(
            "disponibilidad_version_{$appointment->company_id}",
            (int) cache()->get("disponibilidad_version_{$appointment->company_id}", 1) + 1
        );
    }
}
