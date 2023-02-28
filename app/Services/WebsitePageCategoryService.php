<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageCategoryQueryBuilder;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;

class WebsitePageCategoryService extends AbstractService
{
    protected $modelClass = WebsitePageCategory::class;

    protected $modelQueryBuilderClass = WebsitePageCategoryQueryBuilder::class;
}
