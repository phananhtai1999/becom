<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
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
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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


    public function userTeam() {
        return $this->hasMany(UserTeam::class, 'team_uuid', 'uuid');
    }

    public function business() {
        return $this->belongsToMany(BusinessManagement::class, 'business_team', 'team_uuid','business_uuid')->withTimestamps();
    }

    public function getNumOfTeamMemberAttribute()
    {
        return count($this->userTeam);
    }
}
