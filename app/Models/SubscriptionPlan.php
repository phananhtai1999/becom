<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends AbstractModel
{
    use HasFactory, SoftDeletes;

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
        'app_uuid',
        'duration',
        'duration_type',
        'payment_plan_id',
        'payment_method_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'payment_plan_id' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function platformPackage()
    {
        return $this->belongsTo(App::class, 'app_uuid', 'uuid')->withTrashed();
    }

    public function app()
    {
        return $this->belongsTo(App::class, 'app_uuid', 'uuid')->withTrashed();
    }
}
