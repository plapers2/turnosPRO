<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Costumer
 *
 * @property $id
 * @property $name
 * @property $email
 * @property $phone
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Costumer extends Model
{
    use SoftDeletes;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'phone'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
