<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scenario extends AbstractModel
{
    use HasFactory, SoftDeletes;

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
     * @return HasMany
     */
    public function campaignScenarios()
    {
        return $this->hasMany(CampaignScenario::class, 'scenario_uuid', 'uuid');
    }
}
