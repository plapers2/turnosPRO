<?php

namespace App\Services;

use App\Mail\AppointmentCancelledByEmployeeMail;
use App\Mail\AppointmentCompletedMail;
use App\Mail\AppointmentConfirmedByEmployeeMail;
use App\Models\Appointment;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentNotifier
{
    private array $mailables = [
        'confirmed_by_employee' => AppointmentConfirmedByEmployeeMail::class,
        'cancelled_by_employee' => AppointmentCancelledByEmployeeMail::class,
        'completed'             => AppointmentCompletedMail::class,
    ];

    public function send(string $type, Appointment $appointment): void
    {
        $email    = $appointment->customer?->email ?? '';
        $mailable = new ($this->mailables[$type])($appointment);

        try {
            Log::info('Intentando enviar email', ['type' => $type, 'email' => $email]);
            Mail::to($email)->send($mailable);
            Log::info('Email enviado', ['type' => $type]);
            $status = 'sent';
            $error  = null;
        } catch (\Exception $e) {
            Log::error('Error enviando email', ['type' => $type, 'error' => $e->getMessage()]);
            $status = 'error';
            $error  = $e->getMessage();
        }

        NotificationLog::create([
            'appointment_id'  => $appointment->id,
            'type'            => $type,
            'recipient_email' => $email,
            'status'          => $status,
            'error_message'   => $error,
        ]);
    }
}
