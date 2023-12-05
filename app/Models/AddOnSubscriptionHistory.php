<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOnSubscriptionHistory extends Model
{
    use HasFactory;

    protected $table = "add_on_subscription_histories";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'invoice_uuid',
        'add_on_subscription_plan_uuid',
        'subscription_date',
        'expiration_date',
        'payment_method_uuid',
        'billing_address_uuid',
        'logs',
        'app_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'logs' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function addOnSubscriptionPlan() {
        return $this->belongsTo(AddOnSubscriptionPlan::class, 'add_on_subscription_plan_uuid', 'uuid');
    }
    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_uuid', 'uuid');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
    public function invoice() {
        return $this->hasOne(Invoice::class, 'uuid', 'invoice_uuid');
    }
}
