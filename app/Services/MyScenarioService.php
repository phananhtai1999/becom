<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyScenarioQueryBuilder;
use App\Models\Scenario;

class MyScenarioService extends AbstractService
{
    protected $modelClass = Scenario::class;
    protected $modelQueryBuilderClass = MyScenarioQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyScenarioByUuid($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }
}
