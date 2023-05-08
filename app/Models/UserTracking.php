<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;

class UserTracking extends AbstractModel
{
    use HasFactory, SoftDeletes;

    protected $table = "user_trackings";

    protected $primaryKey = "uuid";

    protected $fillable = [
        'ip',
        'location',
        'user_uuid',
        'postal_code',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' => 'integer'
    ];

    protected $appends = [
        'device',
        'browser',
        'platform',
        'is_mobile',
        'is_tablet',
        'is_desktop',
    ];

    public function setUserAgent()
    {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);
        return $agent;
    }

    public function getDeviceAttribute()
    {
        return $this->setUserAgent()->device();
    }

    public function getBrowserAttribute()
    {
        return $this->setUserAgent()->browser();
    }

    public function getPlatformAttribute()
    {
        return $this->setUserAgent()->platform();
    }

    public function getIsMobileAttribute()
    {
        return $this->setUserAgent()->isMobile();
    }

    public function getIsDesktopAttribute()
    {
        return $this->setUserAgent()->isDesktop();
    }

    public function getIsTabletAttribute()
    {
        return $this->setUserAgent()->isTablet();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
