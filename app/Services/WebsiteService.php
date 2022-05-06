<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\Website;

class WebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = WebsiteQueryBuilder::class;
}
