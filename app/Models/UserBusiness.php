<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBusiness extends Model
{
    use HasFactory, SoftDeletes;

    const ACCOUNT_INVITE = 'account';
    const ALREADY_EXISTS_ACCOUNT = 'exists_account';

    /**
     * @var string
     */
    protected $table = "user_business";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'business_uuid',
        'is_blocked',
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

    public function user()
    {
        return $this->hasOne(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    public function business()
    {
        return $this->hasOne(BusinessManagement::class, 'uuid', 'business_uuid');
    }
}
