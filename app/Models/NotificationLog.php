<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'appointment_id',
        'type',
        'recipient_email',
        'status',
        'error_message',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class)->withTrashed();
    }
}
