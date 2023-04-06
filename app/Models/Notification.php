<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    const CAMPAIGN_TYPE = "campaign";
    const SCENARIO_TYPE = "scenario";
    const ACCOUNT_TYPE = "account";
    const START_ACTION = "started";
    const STOP_ACTION = "stopped";

    protected $table = "notifications";

    protected $primaryKey = "uuid";

    protected $fillable = [
        'type',
        'type_uuid',
        'content',
        'user_uuid',
        'read'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'read' => 'boolean',
        'user_uuid' => 'integer',
        'type_uuid' => 'integer',
        'content' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'type_uuid', 'uuid');
    }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'type_uuid', 'uuid');
    }
}
