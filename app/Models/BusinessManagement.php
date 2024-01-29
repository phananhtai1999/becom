<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Resources\UserTeamResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessManagement extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const DEPARTMENT_ENTITY = 'department';
    const LOCATION_ENTITY = 'location';
    const BUSINESS_ENTITY = 'business';
    const TEAM_ENTITY = 'team';
    const USER_ENTITY = 'user';
    const PROJECT_ENTITY = 'project';



    /**
     * @var string
     */
    protected $table = "business_managements";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'introduce',
        'products_services',
        'customers',
        'owner_uuid',
        'domain_uuid',
        'avatar',
        'slogan',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'products_services' => 'array',
        'customers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businessCategories()
    {
        return $this->belongsToMany(BusinessCategory::class, 'management_categories', 'management_uuid', 'category_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class, 'business_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeFromCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeFromUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '<=', $date);
    }

    public function userBusiness() {
        return $this->hasMany(UserBusiness::class, 'business_uuid', 'uuid');
    }

    public function sendProjects() {
        return $this->hasMany(SendProject::class, 'business_uuid', 'uuid');
    }

    public function locations() {
        return $this->hasMany(Location::class, 'business_uuid', 'uuid');
    }

    public function teams() {
        return $this->belongsToMany(Team::class, 'business_team', 'business_uuid','team_uuid')->withTimestamps();
    }

}
