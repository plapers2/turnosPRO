<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function broadcastOn(): array
    {
        // Canal por empresa — así solo los usuarios de esa empresa lo reciben
        return [
            new Channel('appointments.' . $this->appointment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'appointment.updated';
    }

    public function broadcastWith(): array
    {
        $a = $this->appointment->load([
            'customer' => fn($q) => $q->withTrashed(),
            'user'     => fn($q) => $q->withTrashed(),
            'services' => fn($q) => $q->withTrashed(),
        ]);

        return [
            'id'     => $a->id,
            'status' => $a->status,
            // Info mínima para mostrar la notificación toast
            'customer_name'   => $a->customer?->name ?? 'Cliente eliminado',
            'professional'    => $a->user?->name ?? 'Empleado eliminado',
            'start_time'      => $a->start_time->format('d/m/Y H:i'),
        ];
    }
}
