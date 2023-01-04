<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyScenarioQueryBuilder;
use App\Models\Scenario;

class ScenarioService extends AbstractService
{
    protected $modelClass = Scenario::class;

    protected $modelQueryBuilderClass = MyScenarioQueryBuilder::class;
}
