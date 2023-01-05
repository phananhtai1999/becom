<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyScenarioQueryBuilder;
use App\Models\Scenario;

class ScenarioService extends AbstractService
{
    protected $modelClass = Scenario::class;

    protected $modelQueryBuilderClass = MyScenarioQueryBuilder::class;

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
                     'messages' => ['source_null' => "There is only one null value in the source."]];
        }
        $sourceTypes = $arrayIds = [];
        foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $arrayIds[] = $node['id'];
            }else{
                if (!in_array($node['source'], $arrayIds)) {
                    return ['status' => false,
                        'messages' => ['source_parent' => "The parent source above could not be found."]];
                }
                $arrayIds[] = $node['id'];
            }
            if (array_count_values($nodesIds)[$node['id']] >= 2) {
                return ['status' => false,
                    'messages' => ['id_duplicated' => "The selected Ids cannot be duplicated."]];
            }
            if (in_array($node['source'], $nodesIds)) {
                $sourceTypes[$node['source']][] = $node['type'];
            }
            if (!empty($sourceTypes)) {
                $countSourceType = array_count_values($sourceTypes[$node['source']]);
                if ($countSourceType[$node['type']] >= 2) {
                    return ['status' => false,
                        'messages' => ['id_duplicated' => "Must be provide 'type' with the different value for 'source'."]];
                }
            }
        }

        return [
            'status' => true,
            'sourceType' => $sourceTypes
        ];
    }
}
