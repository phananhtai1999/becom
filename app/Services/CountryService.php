<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Country;
use App\Models\QueryBuilders\CountryQueryBuilder;

class CountryService extends AbstractService
{
    protected $modelClass = Country::class;
    protected $modelQueryBuilderClass = CountryQueryBuilder::class;
}
