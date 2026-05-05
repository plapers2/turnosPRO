<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReminder24hJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Appointment::whereIn('status', ['confirmed'])
            ->where('reminder_24h_sent', false)
            ->whereBetween('start_time', [now()->addHours(23), now()->addHours(25)])
            ->with(['customer', 'user', 'company', 'services'])
            ->each(function (Appointment $appointment) {
                Mail::to($appointment->customer->email)
                    ->send(new AppointmentReminderMail($appointment, '24h'));

                $appointment->update(['reminder_24h_sent' => true]);
            });
    }
}
