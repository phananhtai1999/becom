<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ArticleCategory;
use App\Models\QueryBuilders\ArticleCategoryQueryBuilder;

class ArticleCategoryService extends AbstractService
{
    protected $modelClass = ArticleCategory::class;

    protected $modelQueryBuilderClass = ArticleCategoryQueryBuilder::class;
}
