<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "logo",
        "address",
        "phone",
        "state",
        "type_company_id"
    ];

    public function type_companies()
    {
        return $this->belongsTo(TypeCompany::class, 'type_company_id', 'id');
    }
}
