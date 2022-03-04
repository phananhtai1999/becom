<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignTracking;

class CampaignTrackingService extends AbstractService
{
    protected $modelClass = CampaignTracking::class;
}
