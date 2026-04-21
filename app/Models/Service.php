<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
        'image',
        'state',
        'company_id'
    ];

    public function companies(){
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
