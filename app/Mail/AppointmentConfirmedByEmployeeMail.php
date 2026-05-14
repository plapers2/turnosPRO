<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmedByEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
        $this->appointment->loadMissing(['company', 'customer', 'user', 'services']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu cita ha sido confirmada por un profesional'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.appointment-employee-confirmed"
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
