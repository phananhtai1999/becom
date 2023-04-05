<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\TeamQueryBuilder;
use App\Models\Team;

class TeamService extends AbstractService
{
    protected $modelClass = Team::class;

    protected $modelQueryBuilderClass = TeamQueryBuilder::class;
}
