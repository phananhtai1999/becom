<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Services\CampaignScenarioService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scenario extends AbstractModel
{
    use HasFactory, SoftDeletes;

    CONST STATUS_RUNNING = 'running';
    CONST STATUS_STOPPED = 'stopped';

    /**
     * @var string
     */
    protected $table = "scenarios";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_uuid',
        'status',
        'last_stopped_at',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_stopped_at' => 'datetime',
    ];

    public function getNumberCreditAttribute()
    {
        return (new CampaignScenarioService())->calculateNumberCreditOfScenario($this->uuid);
    }

    /**
     * @return HasMany
     */
    public function campaignScenarios()
    {
        return $this->hasMany(CampaignScenario::class, 'scenario_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }
}
