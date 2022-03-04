<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;
}
