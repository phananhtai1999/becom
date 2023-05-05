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

    public function sortByNumberCreditOfMyScenario($request)
    {
        $sortCredit = $request->get('sort');
        $request = $this->getIndexRequest($request);
        if ($sortCredit === 'number_credit') {
            $result =  $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])->get()->sortBy('number_credit');
        }else{
            $result = $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])->get()->sortByDesc('number_credit');
        }

        return $this->collectionPagination($result, $request['per_page'], $request['page']);
    }
}
