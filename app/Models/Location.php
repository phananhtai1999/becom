<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locations';

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'user_uuid',
        'address',
        'app_id',
        'manager_uuid',
        'business_uuid'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'location_uuid', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    public function manager()
    {
        return $this->belongsTo(UserProfile::class, 'manager_uuid', 'user_uuid');
    }

    public function business()
    {
        return $this->belongsTo(BusinessManagement::class, 'business_uuid', 'uuid');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'location_uuid', 'uuid');
    }

    public function sendProjects()
    {
        return $this->belongsToMany(SendProject::class, 'location_send_project', 'location_uuid', 'send_project_uuid')->withTimestamps();
    }
}
