<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddOnSubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "add_on_subscription_plans";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'add_on_uuid',
        'duration',
        'duration_type',
        'payment_plan_id',
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

    public function addOn()
    {
        return $this->belongsTo(AddOn::class, 'add_on_uuid', 'uuid');
    }
}
