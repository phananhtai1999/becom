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
        'name',
        'service',
        'group_api_codes',
        'parent_uuid',
        'monthly',
        'yearly',
        'payment_product_id',
        'description',
        'status',
        'avatar'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'group_api_codes' => 'array',
    ];

    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlan::class, 'app_uuid', 'uuid');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'platform_package_permission', 'platform_package_uuid', 'permission_uuid');
    }

    public function addOns() {
        return $this->hasMany(AddOn::class, 'app_uuid', 'uuid');
    }

    public function parentApp()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    public function childrenApp()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
    }

    public function departments() {
        return $this->belongsToMany(Department::class, 'department_app', 'app_uuid', 'department_uuid');
    }

    public function userApps() {
        return $this->hasMany(UserApp::class, 'app_uuid', 'uuid');
    }
}
