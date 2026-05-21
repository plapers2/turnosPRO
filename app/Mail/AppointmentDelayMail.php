<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentDelayMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Appointment $appointment,
        public readonly int $delayMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Aviso de retraso en tu cita');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.delay');
    }
}
