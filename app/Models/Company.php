<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    protected $fillable = ['name', 'logo', 'email', 'address', 'phone', 'state', 'type_company_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeCompany()
    {
        return $this->belongsTo(\App\Models\TypeCompany::class, 'type_company_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batches()
    {
        return $this->hasMany(\App\Models\Batch::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(\App\Models\Category::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany(\App\Models\Client::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companyUsers()
    {
        return $this->hasMany(\App\Models\CompanyUser::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productionRecords()
    {
        return $this->hasMany(\App\Models\ProductionRecord::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(\App\Models\Role::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany(\App\Models\Sale::class, 'id', 'company_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(\App\Models\Service::class, 'id', 'company_id');
    }
    
}
