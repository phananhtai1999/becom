<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ScenarioRequest;
use App\Http\Resources\ScenarioResourceCollection;
use App\Services\CampaignScenarioService;
use App\Services\CampaignService;
use App\Services\MyScenarioService;
use App\Services\ScenarioService;
use Illuminate\Http\Request;

class ScenarioController extends AbstractRestAPIController
{
    /**
     * @var MyScenarioService
     */
    protected $myService;

    /**
     * @var CampaignScenarioService
     */
    protected $campaignScenarioService;

    /**
     * @param ScenarioService $service
     * @param MyScenarioService $myService
     * @param CampaignScenarioService $campaignScenarioService
     */
    public function __construct(
        ScenarioService $service,
        MyScenarioService $myService,
        CampaignScenarioService $campaignScenarioService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->campaignScenarioService = $campaignScenarioService;
        $this->resourceCollectionClass = ScenarioResourceCollection::class;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyScenario(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                )
            )
        );
    }

    /**
     * @param ScenarioRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeScenario(ScenarioRequest $request)
    {
        $nodes = $request->get('nodes');
        //Validate
        $nodesIds = array_column($nodes, 'id');
        $nodeSources =  array_column($nodes, 'source');
        $NodeIdBySource = array_column($nodes, 'source', 'id');
        $countSourceRoot = count(array_filter($nodeSources, function ($value) {
            return $value === null;}, ARRAY_FILTER_USE_BOTH));

        if ($countSourceRoot < 1 || $countSourceRoot >= 2) {
            return $this->sendValidationFailedJsonResponse(['error' => ['source_null' => "There is only one null value in the source."]]);
        }
        $sourceTypes = $arrayIds = [];
        foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $arrayIds[] = $node['id'];
            }else{
                if (!in_array($node['source'], $arrayIds)) {
                    return $this->sendValidationFailedJsonResponse(['error' => ['source_parent' => "The parent source above could not be found."]]);
                }
                $arrayIds[] = $node['id'];
            }
            if (array_count_values($nodesIds)[$node['id']] >= 2) {
                return $this->sendValidationFailedJsonResponse(['error' => ['id_duplicated' => "The selected Ids cannot be duplicated."]]);
            }
            if (in_array($node['source'], $nodesIds)) {
                $sourceTypes[$node['source']][] = $node['type'];
            }
            if (!empty($sourceTypes)) {
                $countSourceType = array_count_values($sourceTypes[$node['source']]);
                if ($countSourceType[$node['type']] >= 2) {
                    return $this->sendValidationFailedJsonResponse(['error' => ['source_type_duplicate' => "Must be provide 'type' with the different value for 'source'."]]);
                }
            }
        }
        //Insert data
        $scenario = $this->service->create([
            'name' => $request->get('name'),
            'user_uuid' => auth()->user()->getKey()
        ]);

        $typeByNodeId = [];
        foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $campaignScenario = $this->campaignScenarioService->create([
                    'campaign_uuid' => $node['campaign_uuid'],
                    'scenario_uuid' => $scenario->uuid,
                    'parent_uuid' => $node['source'],
                    'type' => $node['type'],
                    'open_within' => $node['open_within']
                ]);
                $typeByNodeId[$node['id']] = [
                    'campaignScenarioUuid' => $campaignScenario->uuid,
                    'type' => $node['type']
                ];
            }else{
                $campaignScenario = $this->campaignScenarioService->create([
                    'campaign_uuid' => $node['campaign_uuid'],
                    'scenario_uuid' => $scenario->uuid,
                    'parent_uuid' => $typeByNodeId[$node['source']]['campaignScenarioUuid'],
                    'type' => $node['type'],
                    'open_within' => $node['open_within']
                ]);
                $typeByNodeId[$node['id']] = [
                    'campaignScenarioUuid' => $campaignScenario->uuid,
                    'type' => $node['type']
                ];
            }
        }

        //Move node type not_open -> last node
        foreach ($typeByNodeId as $nodeId => $value) {
            if ($value['type'] === 'not_open') {
                if (count($sourceTypes[$NodeIdBySource[$nodeId]]) === 2) {
                    $child = $this->campaignScenarioService->findOneById($value['campaignScenarioUuid']);
                    $parent = $this->campaignScenarioService->findOneById($child->parent_uuid);
                    $child->makeLastChildOf($parent);
                }
            }
        }

        return $this->sendOkJsonResponse((['message' => "Create campaign scenario success"]));
    }

    /**
     * @param ScenarioRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyScenario(ScenarioRequest $request)
    {

        $nodes = $request->get('nodes');
        //Validate
        $nodesIds = array_column($nodes, 'id');
        $nodeSources =  array_column($nodes, 'source');
        $NodeIdBySource = array_column($nodes, 'source', 'id');
        $countSourceRoot = count(array_filter($nodeSources, function ($value) {
            return $value === null;}, ARRAY_FILTER_USE_BOTH));

        if ($countSourceRoot < 1 || $countSourceRoot >= 2) {
            return $this->sendValidationFailedJsonResponse(['error' => ['source_null' => "There is only one null value in the source."]]);
        }
        $sourceTypes = $arrayIds = [];
        foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $arrayIds[] = $node['id'];
            }else{
                if (!in_array($node['source'], $arrayIds)) {
                    return $this->sendValidationFailedJsonResponse(['error' => ['source_parent' => "The parent source above could not be found."]]);
                }
                $arrayIds[] = $node['id'];
            }
            if (array_count_values($nodesIds)[$node['id']] >= 2) {
                return $this->sendValidationFailedJsonResponse(['error' => ['id_duplicated' => "The selected Ids cannot be duplicated."]]);
            }
            if (in_array($node['source'], $nodesIds)) {
                $sourceTypes[$node['source']][] = $node['type'];
            }
            if (!empty($sourceTypes)) {
               $countSourceType = array_count_values($sourceTypes[$node['source']]);
                if ($countSourceType[$node['type']] >= 2) {
                    return $this->sendValidationFailedJsonResponse(['error' => ['source_type_duplicate' => "Must be provide 'type' with the different value for 'source'."]]);
                }
            }
        }
        //Insert data
        $scenario = $this->service->create([
            'name' => $request->get('name'),
            'user_uuid' => auth()->user()->getKey()
        ]);

        $typeByNodeId = [];
         foreach ($nodes as $node) {
            if (empty($node['source'])) {
                $campaignScenario = $this->campaignScenarioService->create([
                    'campaign_uuid' => $node['campaign_uuid'],
                    'scenario_uuid' => $scenario->uuid,
                    'parent_uuid' => $node['source'],
                    'type' => $node['type'],
                    'open_within' => $node['open_within']
                ]);
                $typeByNodeId[$node['id']] = [
                    'campaignScenarioUuid' => $campaignScenario->uuid,
                    'type' => $node['type']
                ];
            }else{
                $campaignScenario = $this->campaignScenarioService->create([
                    'campaign_uuid' => $node['campaign_uuid'],
                    'scenario_uuid' => $scenario->uuid,
                    'parent_uuid' => $typeByNodeId[$node['source']]['campaignScenarioUuid'],
                    'type' => $node['type'],
                    'open_within' => $node['open_within']
                ]);
                $typeByNodeId[$node['id']] = [
                    'campaignScenarioUuid' => $campaignScenario->uuid,
                    'type' => $node['type']
                ];
            }
        }

         //Move node type not_open -> last node
        foreach ($typeByNodeId as $nodeId => $value) {
            if ($value['type'] === 'not_open') {
                if (count($sourceTypes[$NodeIdBySource[$nodeId]]) === 2) {
                    $child = $this->campaignScenarioService->findOneById($value['campaignScenarioUuid']);
                    $parent = $this->campaignScenarioService->findOneById($child->parent_uuid);
                    $child->makeLastChildOf($parent);
                }
            }
        }

        return $this->sendOkJsonResponse((['message' => "Create campaign scenario success"]));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyScenario($id)
    {
        $scenario = $this->myService->findMyScenarioByUuid($id);
        $campaignsScenario = $this->campaignScenarioService->showCampaignScenarioByScenarioUuid($id);
        $depth = $this->campaignScenarioService->getMaxDepthOfCampaignScenarioByScenarioUuid($id);

        /*
         * depth sâu nhất = 3 (Mặc định depth = 0 -> x0 = 0)
         * depth = 1 -> (2^1 + 2^2 + 2^3)/2 = 7 => tại vị trí có depth = 1 thì x1 = x0(tại depth:0) +(-) 7
         * depth = 2 -> (2^1 + 2^2)/2 = 3 => tại vị trí có depth = 1 thì x2 = x1(tại depth:0) +(-) 3
         * depth = 3 -> (2^1)/2 = 1 => tại vị trí có depth = 3 thì x3 = x2(tại depth:2) +(-) 1
         * /2 vì mỗi 1 node sẽ có 2 node con
         * */
        $distanceByDepth = $nodes = $coordinatesByUuid = [];
        $exponent = 1;
        for ($i = $depth; $i > 0; $i--) {
            $distanceByDepth[$i] = empty($distanceByDepth[$i + 1]) ? (2 ** $exponent)/2 : (($distanceByDepth[$i + 1])*2 + (2 ** $exponent))/2;
            $exponent ++;
        }

        foreach ($campaignsScenario as $item) {
            if (!empty($item['parent_uuid'])) {
                $coordinatesByUuid[$item['uuid']] = ($item['type'] === 'open') ? $coordinatesByUuid[$item['parent_uuid']] - $distanceByDepth[$item['depth']] : $coordinatesByUuid[$item['parent_uuid']] + $distanceByDepth[$item['depth']];
                $nodes[] = [
                    "uuid" => $item['uuid'],
                    "campaign" => $item['campaign'],
                    "scenario_uuid" => $item['scenario_uuid'],
                    "parent_uuid" => $item['parent_uuid'],
                    "type" => $item['type'],
                    "open_within" => $item['open_within'],
                    "x" => $coordinatesByUuid[$item['uuid']],
                    "y" => $item['depth']
                ];
            }else{
                $coordinatesByUuid[$item['uuid']] = 0;
                $nodes[] = [
                    "uuid" => $item['uuid'],
                    "campaign" =>  $item['campaign'],
                    "scenario_uuid" => $item['scenario_uuid'],
                    "parent_uuid" => $item['parent_uuid'],
                    "type" => $item['type'],
                    "open_within" => $item['open_within'],
                    "x" => 0,
                    "y" => $item['depth']
                ];
            }
        }

        return $this->sendOkJsonResponse(['data' => [
            'name' => $scenario->name,
            'nodes' => $nodes
        ]]);
    }

}
