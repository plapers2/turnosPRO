<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalAvailability extends Model
{
    use SoftDeletes;
    protected $fillable = ['day_of_week', 'start_time', 'end_time', 'user_id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
