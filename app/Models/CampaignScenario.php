<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignScenario extends AbstractModel
{
    use HasFactory, WorksAsNestedSet;

    /**
     * @var string
     */
    protected $table = "campaign_scenario";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Column name which stores reference to parent's node.
     *
     * @var string
     */
    protected $parentColumnName = 'parent_uuid';

    /**
     * Column name for the left index.
     *
     * @var string
     */
    protected $leftColumnName = 'left';

    /**
     * Column name for the right index.
     *
     * @var string
     */
    protected $rightColumnName = 'right';

    /**
     * Column name for the depth field.
     *
     * @var string
     */
    protected $depthColumnName = 'depth';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'parent_uuid',
        'campaign_uuid',
        'scenario_uuid',
        'type',
        'open_within'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function parentCampaignScenario()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return Collection
     */
    public function childrenCampaignScenario()
    {
        return $this->children()->orderBy('created_at', 'DESC')->get();
    }

}
