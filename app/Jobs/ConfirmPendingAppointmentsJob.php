<?php

namespace App\Jobs;

use App\Mail\AppointmentAutoConfirmedMail;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ConfirmPendingAppointmentsJob implements ShouldQueue
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
        Appointment::where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->with(['customer', 'user', 'company', 'services'])
            ->each(function (Appointment $appointment) {
                $appointment->update(['status' => 'confirmed']);

                Mail::to($appointment->customer->email)
                    ->send(new AppointmentAutoConfirmedMail($appointment));
            });
    }
}
