<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User    $admin,
        public Company $company,
        public string  $tempPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a TurnosPRO — Tus credenciales de acceso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-credentials',
        );
    }
}
