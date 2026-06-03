<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendReminder24hJob;
use App\Jobs\SendReminder1hJob;

// Recordatorio 24h: cada hora con ventana [23h, 24h] — no depende de medianoche
Schedule::job(new SendReminder24hJob)->hourly();

// Recordatorio 1h: cada hora
Schedule::job(new SendReminder1hJob)->hourly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
