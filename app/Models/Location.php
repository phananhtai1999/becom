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
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
    public function business()
    {
        return $this->belongsTo(BusinessManagement::class, 'business_uuid', 'uuid');
    }

    public function sendProjects()
    {
        return $this->belongsToMany(SendProject::class, 'location_send_project', 'location_uuid', 'send_project_uuid')->withTimestamps();
    }
}
