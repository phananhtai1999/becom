<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PlatformPackage;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\PlatformPackageQueryBuilder;

class PlatformPackageService extends AbstractService
{
    protected $modelClass = PlatformPackage::class;
    protected $modelQueryBuilderClass = PlatformPackageQueryBuilder::class;

}
