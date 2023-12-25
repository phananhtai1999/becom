<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingAddress extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "billing_addresses";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_uuid',
        'email',
        'address',
        'phone',
        'company',
        'country',
        'city',
        'state',
        'zipcode',
        'type',
        'is_default',
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

    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
