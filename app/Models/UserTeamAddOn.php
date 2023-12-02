<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTeamAddOn extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE_STATUS = 'active';
    const DISABLE_STATUS = 'disable';

    /**
     * @var string
     */
    protected $table = "user_team_add_on";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_team_uuid',
        'add_on_uuid',
        'status'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function addOn()
    {
        return $this->belongsTo(AddOn::class, 'uuid', 'add_on');
    }
}
