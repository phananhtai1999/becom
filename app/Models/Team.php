<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const LINK_INVITE = 'link';
    const ACCOUNT_INVITE = 'account';
    const ALREADY_EXISTS_ACCOUNT = 'exists_account';
    /**
     * @var string
     */
    protected $table = "teams";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'owner_uuid',
        'department_uuid',
        'location_uuid',
        'parent_team_uuid',
        'leader_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'leader_uuid' => 'integer'
    ];
    /**
     * @var string[]
     */
    protected $appends = [
        'NumOfTeamMember'
    ];
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_uuid', 'uuid');
    }

    public function childrenTeam()
    {
        return $this->hasMany(__CLASS__, 'parent_team_uuid');
    }

    public function parentTeam()
    {
        return $this->belongsTo(__CLASS__, 'parent_team_uuid');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_uuid');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_uuid');
    }


    public function userTeam() {
        return $this->hasMany(UserTeam::class, 'team_uuid', 'uuid');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_teams', 'team_uuid','user_uuid')->withTimestamps();
    }

    public function business() {
        return $this->belongsToMany(BusinessManagement::class, 'business_team', 'team_uuid','business_uuid')->withTimestamps();
    }

    public function addOns() {
        return $this->belongsToMany(AddOn::class, 'team_add_on', 'team_uuid','add_on_uuid')->withTimestamps();
    }

    public function getNumOfTeamMemberAttribute()
    {
        return count($this->userTeam);
    }

    public function scopeTeamRoot(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('parent_team_uuid');
        }
    }

    public function sendProjects()
    {
        return $this->belongsToMany(SendProject::class, 'team_send_project', 'team_uuid', 'send_project_uuid')->withTimestamps();
    }
}
