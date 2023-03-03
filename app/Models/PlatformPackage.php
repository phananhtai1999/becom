<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformPackage extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * @var string
     */
    protected $table = "platform_packages";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'monthly',
        'yearly',
        'payment_product_id',
        'description'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlan::class, 'platform_package_uuid', 'uuid');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'platform_package_permission', 'platform_package_uuid', 'permission_uuid');
    }
}
