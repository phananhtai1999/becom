<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;

class Order extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const ORDER_PENDING_REQUEST_STATUS = 1;
    const ORDER_PAYMENT_SUCCESS_STATUS = 2;
    const ORDER_PAYPAL_PAYMENT_METHOD = 1;
    const ORDER_STRIPE_PAYMENT_METHOD = 2;
    const ORDER_MOMO_PAYMENT_METHOD = 3;

    /**
     * @var string
     */
    protected $table = "orders";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'payment_method_uuid',
        'credit',
        'total_price',
        'status',
        'note',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'integer',
        'total_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
