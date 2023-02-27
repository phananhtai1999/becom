<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformPackage extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "platform_packages";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'monthly',
        'yearly',
        'payment_product_id'
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
