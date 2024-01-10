<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Techup\ApiConfig\Models\Config;

class UserProfile extends \Techup\ApiBase\Models\UserProfile
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "becom_user_profiles";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'email_verified_at',
        'email_verification_code',
        'banned_at',
        'avatar_img',
        'cover_img',
        'credit',
        'can_add_smtp_account',
        'can_remove_footer_template',
        'app_id',
        'user_uuid',
        'first_name',
        'last_name',
        'email'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'can_remove_footer_template' => 'boolean'
    ];

    protected $appends = [
        'name',
        'avatar_img_absolute',
        'cover_img_absolute ',
        'platform_package',
        'team'
    ];

    /**
     * @return string
     */
    public function getAvatarImgAbsoluteAttribute()
    {
        return !empty($this->avatar_img) ? Storage::disk('s3')->url($this->avatar_img) : $this->getValueAvatarDefault();
    }

    /**
     * @return string
     */
    public function getValueAvatarDefault()
    {
        return optional(Config::where('key', 'user_default_avatar')->first())->value;
    }

    /**
     * @return string
     */
    public function getCoverImgAbsoluteAttribute()
    {
        return !empty($this->cover_img) ? Storage::disk('s3')->url($this->cover_img) : null;
    }
}
