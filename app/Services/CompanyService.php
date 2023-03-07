<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\QueryBuilders\CompanyQueryBuilder;

class CompanyService extends AbstractService
{
    protected $modelClass = Company::class;

    protected $modelQueryBuilderClass = CompanyQueryBuilder::class;
}
