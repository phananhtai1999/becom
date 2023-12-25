<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $table = "otps";

    /**
     * @var string
     */
    protected $primaryKey = 'user_uuid';
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'active_code',
        'user_uuid',
        'expired_time',
        'blocked_time',
        'refresh_time',
        'refresh_count',
        'wrong_count',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'expired_time' => 'datetime',
        'blocked_time' => 'datetime',
        'refresh_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
