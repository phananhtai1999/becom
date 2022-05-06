<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\CampaignQueryBuilder;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = CampaignQueryBuilder::class;
}
