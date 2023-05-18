<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ArticleCategory;
use App\Models\BusinessCategory;
use App\Models\Purpose;
use App\Models\QueryBuilders\ArticleCategoryQueryBuilder;
use App\Models\QueryBuilders\BusinessCategoryQueryBuilder;
use App\Models\QueryBuilders\PurposeQueryBuilder;

class PurposeService extends AbstractService
{
    protected $modelClass = Purpose::class;

    protected $modelQueryBuilderClass = PurposeQueryBuilder::class;
}
