<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "subscription_plans";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'platform_package_uuid',
        'duration',
        'duration_type',
        'payment_plan_id',
        'payment_method_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
