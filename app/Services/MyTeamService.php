<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyTeamQueryBuilder;
use App\Models\Team;

class MyTeamService extends AbstractService
{
    protected $modelClass = Team::class;

    protected $modelQueryBuilderClass = MyTeamQueryBuilder::class;

}
