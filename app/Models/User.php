<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'email_verification_code',
        'banned_at',
        'avatar_img',
        'cover_img',
        'credit',
        'can_add_smtp_account'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
        'avatar_img_absolute',
        'cover_img_absolute ',
    ];

    /**
     * @return string
     */
    public function getAvatarImgAbsoluteAttribute()
    {
        return !empty($this->avatar_img) ? Storage::disk('s3')->url($this->avatar_img) : '';
    }

    /**
     * @return string
     */
    public function getCoverImgAbsoluteAttribute()
    {
        return !empty($this->cover_img) ? Storage::disk('s3')->url($this->cover_img) : '';
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_uuid', 'role_uuid');
    }

    /**
     * @return HasOne
     */
    public function userDetails()
    {
        return $this->hasOne(UserDetail::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasOne
     */
    public function userConfig()
    {
        return $this->hasOne(UserConfig::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function userAccessTokens()
    {
        return $this->hasMany(UserAccessToken::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function websites()
    {
        return $this->hasMany(Website::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function contactLists()
    {
        return $this->hasMany(ContactList::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function userCreditHistories()
    {
        return $this->hasMany(UserCreditHistory::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function creditTransactionHistories()
    {
        return $this->hasMany(CreditTransactionHistory::class, 'user_uuid', 'uuid');
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return ($this->first_name) . ' ' . ($this->last_name);
    }
}
