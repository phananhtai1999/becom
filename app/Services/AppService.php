<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\App;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\AppQueryBuilder;

class AppService extends AbstractService
{
    protected $modelClass = App::class;
    protected $modelQueryBuilderClass = AppQueryBuilder::class;

    public function checkIncludePlatform($platformPackageUuid)
    {
        if (auth()->user()->platform_package == 'professional' && in_array($platformPackageUuid, App::PROFESSIONAL_INCLUDE)) {

            return true;
        }elseif (auth()->user()->platform_package == 'business' && in_array($platformPackageUuid, App::BUSINESS_INCLUDE)) {

            return true;
        }

        return false;
    }

}
