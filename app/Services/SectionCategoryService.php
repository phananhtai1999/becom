<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SectionCategoryQueryBuilder;
use App\Models\SectionCategory;

class SectionCategoryService extends AbstractService
{
    protected $modelClass = SectionCategory::class;

    protected $modelQueryBuilderClass = SectionCategoryQueryBuilder::class;
}
