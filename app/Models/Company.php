<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        "name",
        "logo",
        "address",
        "phone",
        "state",
        "email",
        "type_company_id"
    ];

    public function type_companies()
    {
        return $this->belongsTo(TypeCompany::class, 'type_company_id', 'id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
