<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ConfirmPendingAppointmentsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ConfirmPendingAppointmentsJob)->hourly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
