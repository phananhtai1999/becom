<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddOn extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "user_add_ons";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'add_on_subscription_plan_uuid',
        'expiration_date',
        'auto_renew'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function addOnSubscriptionPlan() {
        return $this->belongsTo(AddOnSubscriptionPlan::class, 'add_on_subscription_plan_uuid', 'uuid');
    }
}
