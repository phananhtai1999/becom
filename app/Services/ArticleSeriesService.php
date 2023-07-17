<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ArticleSeries;
use App\Models\QueryBuilders\ArticleSeriesQueryBuilder;

class ArticleSeriesService extends AbstractService
{
    protected $modelClass = ArticleSeries::class;

    protected $modelQueryBuilderClass = ArticleSeriesQueryBuilder::class;
}
