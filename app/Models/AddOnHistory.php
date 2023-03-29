<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOnHistory extends Model
{
    use HasFactory;

    protected $table = "add_on_histories";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'add_on_uuid',
        'subscription_date',
        'expiration_date',
        'payment_method_uuid',
        'logs',
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
}
