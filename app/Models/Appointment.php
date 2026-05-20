<?php

namespace App\Models;

use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Appointment
 *
 * @property $id
 * @property $start_time
 * @property $end_time
 * @property $cancellation_reason
 * @property $payment_expires_at
 * @property $notes
 * @property $cancel_token
 * @property $cancel_token_expires_at
 * @property $customer_id
 * @property $user_id
 * @property $company_id
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 *
 * @property Company $company
 * @property Customer $customer
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Appointment extends Model
{
    use SoftDeletes;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'cancellation_reason',
        'payment_expires_at',
        'notes',
        'cancel_token',
        'cancel_token_expires_at',
        'customer_id',
        'user_id',
        'cancelled_by',
        'confirmed_by',
        'completed_by',
        'previous_user',
        'completed_at',
        'company_id',
        'status',
        'reminder_24h_sent',
        'reminder_1h_sent',
        'booking_group',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::observe(AppointmentObserver::class);
    }

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_CONFIRMED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_service', 'appointment_id', 'service_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function previousUser()
    {
        return $this->belongsTo(User::class, 'previous_user');
    }


    public function statusLogs()
    {
        return $this->hasMany(AppointmentStatusLog::class)->orderBy('created_at');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────


    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('start_time', '>=', $from);
        if ($to)   $query->whereDate('start_time', '<=', $to);
        return $query;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getDurationInMinutes(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED]);
    }
}
