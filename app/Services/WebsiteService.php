<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageCategoryQueryBuilder;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;

class WebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = WebsiteQueryBuilder::class;
}
