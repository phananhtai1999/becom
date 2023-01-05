<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\EditScenarioRequest;
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
        $NodeIdBySource = array_column($nodes, 'source', 'id');

        //Validate
        $validateNodes = $this->service->validateScenario($nodes);

        if (empty($validateNodes['status'])) {
            return $this->sendOkJsonResponse(["errors" => $validateNodes['messages']]);
        }
        $sourceTypes = $validateNodes['sourceType'];

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
        $NodeIdBySource = array_column($nodes, 'source', 'id');

        //Validate
        $validateNodes = $this->service->validateScenario($nodes);

        if (empty($validateNodes['status'])) {
            return $this->sendOkJsonResponse(["errors" => $validateNodes['messages']]);
        }
        $sourceTypes = $validateNodes['sourceType'];

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

    /**
     * @param EditScenarioRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyScenario(EditScenarioRequest $request, $id)
    {
        $scenario = $this->myService->findMyScenarioByUuid($id);
        $this->service->update($scenario, [
            'name' => $request->get('name')
        ]);

        $nodes = $request->get('nodes');
        $NodeIdBySource = array_column($nodes, 'source', 'id');

        //Validate
        $validateNodes = $this->service->validateScenario($nodes);

        if (empty($validateNodes['status'])) {
            return $this->sendOkJsonResponse(["errors" => $validateNodes['messages']]);
        }
        $sourceTypes = $validateNodes['sourceType'];

        $nodesIdUUid = array_column($nodes, 'uuid', 'id');
        foreach ( $nodesIdUUid as $item) {
            if (!empty($item)) {
                $nodesUuid [] = $item;
            }
        }

        //Update Nodes
        $campaignScenarioDelete = $this->campaignScenarioService->getCampaignsScenarioExistsInUUidByScenarioUuid($nodesUuid, $id);
        if (!empty($campaignScenarioDelete)) {
            foreach ($campaignScenarioDelete as $item) {
                $item->delete();
            }
        }

        foreach ($nodes as $node) {
            if (!empty($node['uuid'])) {
                $campaignScenario = $this->campaignScenarioService->findOneById($node['uuid']);
                $this->campaignScenarioService->update($campaignScenario, [
                    'campaign_uuid' => $node['campaign_uuid'],
//                    'scenario_uuid' => $id,
//                    'parent_uuid' => $node['source'],
//                    'type' => $node['type'],
                    'open_within' => $node['open_within']
                ]);
                $typeByNodeId[$node['id']] = [
                    'campaignScenarioUuid' => $node['uuid'],
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
            if (empty($nodesIdUUid[$nodeId]) && $value['type'] === 'not_open' && count($sourceTypes[$NodeIdBySource[$nodeId]]) === 2) {
                $child = $this->campaignScenarioService->findOneById($value['campaignScenarioUuid']);
                $parent = $this->campaignScenarioService->findOneById($child->parent_uuid);
                $child->makeLastChildOf($parent);
            }
        }

        return $this->sendOkJsonResponse((['message' => "Edit campaign scenario success"]));
    }

}
