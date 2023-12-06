<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "invoices";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'billing_address_uuid',
        'user_uuid',
        'product_data',
        'payment_method_uuid',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'product_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user() {
        return $this->hasOne(User::class, 'uuid', 'user_uuid');
    }

    public function paymentMethod() {
        return $this->hasOne(PaymentMethod::class, 'uuid', 'payment_method_uuid');
    }

    public function billingAddress() {
        return $this->hasOne(BillingAddress::class, 'uuid', 'billing_address_uuid');
    }

}
