<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\ScenarioQueryBuilder;
use App\Models\Scenario;

class ScenarioService extends AbstractService
{
    protected $modelClass = Scenario::class;

    protected $modelQueryBuilderClass = ScenarioQueryBuilder::class;

    /**
     * @param $nodes
     * @return array
     */
    public function validateScenario($nodes)
    {
        $nodesIds = array_column($nodes, 'id');
        $nodeSources =  array_column($nodes, 'source');
        $countSourceRoot = count(array_filter($nodeSources, function ($value) {
            return $value === null;}, ARRAY_FILTER_USE_BOTH));

        if ($countSourceRoot < 1 || $countSourceRoot >= 2) {
            return ['status' => false,
                     'messages' => ['source_null' => __('messages.source_only_one_null')]];
        }
        $sourceTypes = $arrayIds = [];
        foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $arrayIds[] = $node['id'];
            }else{
                if (!in_array($node['source'], $arrayIds)) {
                    return ['status' => false,
                        'messages' => ['source_parent' => __('messages.parent_source_not_found')]];
                }
                $arrayIds[] = $node['id'];
            }
            if (array_count_values($nodesIds)[$node['id']] >= 2) {
                return ['status' => false,
                    'messages' => ['id_duplicated' => __('messages.id_duplicated')]];
            }
            if (in_array($node['source'], $nodesIds)) {
                $sourceTypes[$node['source']][] = $node['type'];
            }
            if (!empty($sourceTypes)) {
                $countSourceType = array_count_values($sourceTypes[$node['source']]);
                if ($countSourceType[$node['type']] >= 2) {
                    return ['status' => false,
                        'messages' => ['type_source' => __('messages.type_source_error')]];
                }
            }
        }

        return [
            'status' => true,
            'sourceType' => $sourceTypes
        ];
    }

    public function sortByNumberCreditOfScenario($request)
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
