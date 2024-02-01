<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Techup\ApiList\Models\GroupApiList;

class App extends Model
{
    use HasFactory,SoftDeletes;
    const DEFAULT_PLATFORM_PACKAGE_1 = 'starter';
    const DEFAULT_PLATFORM_PACKAGE_2 = 'professional';
    const DEFAULT_PLATFORM_PACKAGE_3 = 'business';

    const PLATFORM_PACKAGE_DRAFT = 'draft';
    const PLATFORM_PACKAGE_PUBLISH = 'publish';
    const PLATFORM_PACKAGE_DISABLE = 'disable';

    const STARTER_INCLUDE = [];
    const PROFESSIONAL_INCLUDE = ['starter'];
    const BUSINESS_INCLUDE = ['professional', 'starter'];
    const DURATION_MONTH = 'month';
    const DURATION_YEAR = 'year';
    /**
     * @var string
     */
    protected $table = "apps";

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
        'description',
        'status'
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

    public function groupApis() {
        return $this->belongsToMany(GroupApiList::class, 'app_group_api', 'app_uuid', 'group_api_uuid');
    }

    public function addOns() {
        return $this->hasMany(AddOn::class, 'app_uuid', 'uuid');
    }
}
