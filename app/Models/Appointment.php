<?php

namespace App\Models;

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
        'company_id',
        'booking_group'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];


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
}
