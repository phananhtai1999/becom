<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
        'can_add_smtp_account',
        'can_remove_footer_template'
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
        'can_remove_footer_template' => 'boolean'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
        'avatar_img_absolute',
        'cover_img_absolute ',
        'platform_package',
        'team',
        'full_name',
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' '. $this->last_name;
    }

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

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_uuid', 'role_uuid')->withTimestamps();
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
    public function sendProjects()
    {
        return $this->hasMany(SendProject::class, 'user_uuid', 'uuid');
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

    /**
     * @param Builder $query
     * @param $bannedAt
     * @return Builder
     */
    public function scopeFromBannedAt(Builder $query, $bannedAt): Builder
    {
        return $query->whereDate('banned_at', '>=', $bannedAt);
    }

    /**
     * @param Builder $query
     * @param $bannedAt
     * @return Builder
     */
    public function scopeToBannedAt(Builder $query, $bannedAt): Builder
    {
        return $query->whereDate('banned_at', '<=', $bannedAt);
    }

    /**
     * @return HasOne
     */
    public function userPlatformPackage()
    {
        return $this->hasOne(UserPlatformPackage::class, 'user_uuid', 'uuid');
    }

    public function userAddOns()
    {
        return $this->hasMany(UserAddOn::class, 'user_uuid', 'uuid');
    }

    public function userTeams()
    {
        return $this->hasMany(UserTeam::class, 'user_uuid', 'uuid');
    }

    /**
     * @return null
     */
    public function getPlatformPackageAttribute()
    {
        return $this->userPlatformPackage->platform_package_uuid ?? null;
    }

    /**
     * @return null
     */
    public function getTeamAttribute()
    {
        return $this->userTeam->team_uuid ?? null;
    }

    /**
     * @return HasMany
     */
    public function reminds()
    {
        return $this->hasMany(Remind::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function positions()
    {
        return $this->hasMany(Position::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function status()
    {
        return $this->hasMany(Status::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasOne
     */
    public function partner()
    {
        return $this->hasOne(Partner::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function businessManagements()
    {
        return $this->hasMany(BusinessManagement::class, 'owner_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class, 'owner_uuid', 'uuid');
    }

    public function userTeam()
    {
        return $this->hasOne(UserTeam::class, 'user_uuid', 'uuid');
    }

    public function partnerUser()
    {
        return $this->hasOne(PartnerUser::class, 'user_uuid', 'uuid');
    }

    public function invite()
    {
        return $this->hasOne(Invite::class, 'user_uuid', 'uuid');
    }

    public function userTeamContactLists() {
        return $this->belongsToMany(ContactList::class, 'user_team_contact_lists', 'user_uuid', 'contact_list_uuid')->withTimestamps('created_at');
    }

    public function userTrackings()
    {
        return $this->hasMany(UserTracking::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function articleSeries()
    {
        return $this->hasMany(ArticleSeries::class, 'assigned_ids', 'uuid');
    }
}
