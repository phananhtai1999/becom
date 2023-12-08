<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocialProfile extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "user_social_profiles";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'social_network_uuid',
        'social_profile_key',
        'other_data',
        'social_profile_name',
        'social_profile_avatar',
        'social_profile_email',
        'social_profile_phone',
        'social_profile_address',
        'updated_info_at',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'other_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'updated_info_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
