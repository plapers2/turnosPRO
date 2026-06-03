<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInvitation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'invited_by',
        'token',
        'email',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->trashed();
    }

    public function isUsable(): bool
    {
        return !$this->isRevoked()
            && !$this->isExpired()
            && $this->status !== 'registered';
    }
}