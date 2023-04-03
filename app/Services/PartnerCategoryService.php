<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerCategory;
use App\Models\QueryBuilders\PartnerCategoryQueryBuilder;
use App\Models\QueryBuilders\SectionCategoryQueryBuilder;
use App\Models\SectionCategory;

class PartnerCategoryService extends AbstractService
{
    protected $modelClass = PartnerCategory::class;

    protected $modelQueryBuilderClass = PartnerCategoryQueryBuilder::class;
}
