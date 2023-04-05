<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerTracking;
use App\Models\QueryBuilders\PartnerTrackingQueryBuilder;

class PartnerTrackingService extends AbstractService
{
    protected $modelClass = PartnerTracking::class;

    protected $modelQueryBuilderClass = PartnerTrackingQueryBuilder::class;
}
