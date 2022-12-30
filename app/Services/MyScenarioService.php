<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Scenario;

class MyScenarioService extends AbstractService
{
    protected $modelClass = Scenario::class;

}
