<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelayNotification extends Model
{
    protected $fillable = [
        'company_id',
        'sent_by',
        'delay_minutes',
        'recipients_count',
        'status',
    ];

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }
}
