<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\QueryBuilders\PartnerCategoryQueryBuilder;
use App\Models\QueryBuilders\PartnerQueryBuilder;
use App\Models\QueryBuilders\SectionCategoryQueryBuilder;
use App\Models\SectionCategory;

class PartnerService extends AbstractService
{
    protected $modelClass = Partner::class;

    protected $modelQueryBuilderClass = PartnerQueryBuilder::class;
}
