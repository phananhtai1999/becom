<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Article;
use App\Models\QueryBuilders\ArticleQueryBuilder;
use App\Models\UserPlatformPackage;

class UserPlatformPackageService extends AbstractService
{
    protected $modelClass = UserPlatformPackage::class;

    protected $modelQueryBuilderClass = ArticleQueryBuilder::class;

    public function checkPurchasedPlatform($platformPackageUuid)
    {
        return $this->model->where([
           'user_uuid' => auth()->user(),
            'app_id' => auth()->appId(),
            'platform_package_uuid' => $platformPackageUuid,
        ])->first();
    }
}
