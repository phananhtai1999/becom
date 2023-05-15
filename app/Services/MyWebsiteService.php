<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyWebsiteQueryBuilder;
use App\Models\QueryBuilders\WebsitePageCategoryQueryBuilder;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;

class MyWebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = MyWebsiteQueryBuilder::class;

    public function showMyWebsite($uuid)
    {
        return $this->findOneWhereOrFail([
           ['user_uuid' , auth()->user()->getKey()],
           ['uuid' , $uuid]
        ]);
    }
}
