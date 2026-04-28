<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * Class Company
 *
 * @property $id
 * @property $name
 * @property $logo
 * @property $email
 * @property $address
 * @property $phone
 * @property $state
 * @property $type_company_id
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 *
 * @property TypeCompany $typeCompany
 * @property Batch[] $batches
 * @property Category[] $categories
 * @property Client[] $clients
 * @property CompanyUser[] $companyUsers
 * @property Expense[] $expenses
 * @property ProductionRecord[] $productionRecords
 * @property Product[] $products
 * @property Role[] $roles
 * @property Sale[] $sales
 * @property CompanyUser[] $companyUsers
 * @property Service[] $services
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Company extends Model
{
    use SoftDeletes, HasFactory;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'logo', 'email', 'address', 'phone', 'type_company_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeCompany()
    {
        return $this->belongsTo(\App\Models\TypeCompany::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
