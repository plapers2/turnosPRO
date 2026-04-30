<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ConfirmPendingAppointmentsJob;
use App\Jobs\SendReminder24hJob;
use App\Jobs\SendReminder1hJob;

Schedule::job(new ConfirmPendingAppointmentsJob)->hourly();
Schedule::job(new SendReminder24hJob)->daily();
Schedule::job(new SendReminder1hJob)->hourly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
