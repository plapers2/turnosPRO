<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OpeningHour
 *
 * @property $id
 * @property $day_of_week
 * @property $start_time
 * @property $end_time
 * @property $duration
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class OpeningHour extends Model
{
    use SoftDeletes;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['day_of_week', 'start_time', 'end_time', 'company_id'];

    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
