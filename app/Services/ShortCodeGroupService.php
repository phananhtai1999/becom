<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\ShortCodeGroupQueryBuilder;
use App\Models\QueryBuilders\WebsitePageShortCodeQueryBuilder;
use App\Models\Role;
use App\Models\ShortCodeGroup;
use App\Models\WebsitePageShortCode;

class ShortCodeGroupService extends AbstractService
{
    protected $modelClass = ShortCodeGroup::class;

    protected $modelQueryBuilderClass = ShortCodeGroupQueryBuilder::class;
}
