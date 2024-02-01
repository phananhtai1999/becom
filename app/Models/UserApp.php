<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserApp extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "user_app";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'app_uuid',
        'subscription_plan_uuid',
        'expiration_date',
        'auto_renew',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function platformPackage() {
        return $this->belongsTo(App::class, 'app_uuid', 'uuid');
    }

    public function app() {
        return $this->belongsTo(App::class, 'app_uuid', 'uuid');
    }
    public function subscriptionPlan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_uuid', 'uuid');
    }
}
