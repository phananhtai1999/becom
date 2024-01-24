<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Role extends AbstractModel
{
    use HasFactory, SoftDeletes, HasSlug;

    const ROLE_ROOT = 'app_system';
    const ROLE_ADMIN = 'app_admin';
    const ROLE_EDITOR = 'app_editor';
    const ROLE_USER = 'app_user';
    const ROLE_USER_MANAGER = 'user_manager';
    const ROLE_USER_MEMBER= 'user_member';
    const ROLE_USER_OWNER = 'user_owner';
    const ROLE_USER_LEADER = 'user_leader';
    const ROLE_PARTNER = 'app_partner';

    /**
     * @var string
     */
    protected $table = "roles";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // spatie/laravel-sluggable package.
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->usingSeparator('-');
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_uuid', 'user_uuid')->withTimestamps();
    }
}
