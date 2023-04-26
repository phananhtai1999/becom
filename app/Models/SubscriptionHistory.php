<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "subscription_histories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'subscription_date',
        'expiration_date',
        'logs',
        'status',
        'user_uuid',
        'subscription_plan_uuid',
        'payment_method_uuid',
        'billing_address_uuid',
        'invoice_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'logs' => 'array'
    ];
    public function subscriptionPlan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_uuid', 'uuid');
    }
    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_uuid', 'uuid');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}

