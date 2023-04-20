<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTeam extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * @var string
     */
    protected $table = "user_teams";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'team_uuid',
        'permission_uuids',
        'is_blocked'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'permission_uuids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'user_uuid');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'uuid', 'team_uuid');
    }

    public function permissions()
    {
        return Permission::whereIn('uuid', $this->permission_uuids ?? [])->get();
    }
}
