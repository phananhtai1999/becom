<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    const LINK_INVITE = 'link';
    const ACCOUNT_INVITE = 'account';
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
    public function getNumOfTeamMemberAttribute()
    {
        return count($this->userTeam);
    }
}
