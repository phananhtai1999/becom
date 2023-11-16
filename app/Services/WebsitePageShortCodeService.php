<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageShortCodeQueryBuilder;
use App\Models\Role;
use App\Models\WebsitePageShortCode;

class WebsitePageShortCodeService extends AbstractService
{
    protected $modelClass = WebsitePageShortCode::class;

    protected $modelQueryBuilderClass = WebsitePageShortCodeQueryBuilder::class;
}
