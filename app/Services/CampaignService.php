<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use Carbon\Carbon;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = CampaignQueryBuilder::class;

    /**
     * @return mixed|void
     */
    public function loadActiveCampaign()
    {
        return $this->findOneWhereOrFail([
            ['from_date', '<=', Carbon::now()],
            ['to_date', '>=', Carbon::now()],
            ['was_finished', false],
            ['was_stopped_by_owner', false],
            ['is_running', false],
        ]);
    }

}
