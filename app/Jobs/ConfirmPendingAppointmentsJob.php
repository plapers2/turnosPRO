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

                // Actualizar sin disparar el Observer automático para evitar
                // que auth()->id() quede null en el log
                $appointment->status = 'confirmed';
                $appointment->saveQuietly();

                // Registrar el log manualmente con actor = sistema (null)
                \App\Models\AppointmentStatusLog::create([
                    'appointment_id' => $appointment->id,
                    'changed_by'     => null,   // null = sistema automático
                    'from_status'    => 'pending',
                    'to_status'      => 'confirmed',
                    'reason'         => 'Confirmación automática — 24h sin gestión',
                ]);

                // Registrar en NotificationLog y enviar correo
                try {
                    \Illuminate\Support\Facades\Mail::to($appointment->customer->email)
                        ->send(new \App\Mail\AppointmentAutoConfirmedMail($appointment));

                    \App\Models\NotificationLog::create([
                        'appointment_id'  => $appointment->id,
                        'type'            => 'auto_confirmed',
                        'recipient_email' => $appointment->customer->email,
                        'status'          => 'sent',
                        'error_message'   => null,
                    ]);
                } catch (\Exception $e) {
                    \App\Models\NotificationLog::create([
                        'appointment_id'  => $appointment->id,
                        'type'            => 'auto_confirmed',
                        'recipient_email' => $appointment->customer->email,
                        'status'          => 'error',
                        'error_message'   => $e->getMessage(),
                    ]);
                }
            });
    }
}
