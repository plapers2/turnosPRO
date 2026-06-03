<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReassigned extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(public Appointment $appointment)
    {
        $this->appointment->loadMissing(['company', 'customer', 'user', 'services']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'El profesional de tu cita ha sido cambiado'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.appointment-reassigned"
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
