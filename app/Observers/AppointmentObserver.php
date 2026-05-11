<?php

namespace App\Observers;

use App\Events\AppointmentUpdated;
use App\Models\Appointment;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    public function updated(Appointment $appointment): void
    {
        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }

    public function deleted(Appointment $appointment): void
    {
        broadcast(new AppointmentUpdated($appointment))->toOthers();
    }
}
