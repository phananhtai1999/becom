<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Group;
use App\Models\QueryBuilders\GroupQueryBuilder;

class GroupService extends AbstractService
{
    protected $modelClass = Group::class;

    protected $modelQueryBuilderClass = GroupQueryBuilder::class;
}