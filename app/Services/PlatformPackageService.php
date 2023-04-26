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

    public function checkIncludePlatform($platformPackageUuid)
    {
        if (auth()->user()->platform_package == 'professional' && in_array($platformPackageUuid, PlatformPackage::PROFESSIONAL_INCLUDE)) {

            return true;
        }elseif (auth()->user()->platform_package == 'business' && in_array($platformPackageUuid, PlatformPackage::BUSINESS_INCLUDE)) {

            return true;
        }

        return false;
    }

}
