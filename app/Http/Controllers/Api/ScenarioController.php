<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\CalculateCreditWhenStopScenarioEvent;
use App\Events\SendByCampaignRootScenarioEvent;
use App\Events\SendNotificationSystemEvent;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Requests\EditScenarioRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ScenarioRequest;
use App\Http\Requests\StatusMyScenarioRequest;
use App\Http\Resources\ScenarioResourceCollection;
use App\Models\Notification;
use App\Services\CampaignScenarioService;
use App\Services\CampaignService;
use App\Services\ConfigService;
use App\Services\ContactService;
use App\Services\MailSendingHistoryService;
use App\Services\MyScenarioService;
use App\Services\ScenarioService;
use App\Services\UserService;
use Carbon\Carbon;
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

    protected $mailSendingHistoryService;

    protected $campaignService;

    protected $userService;

    protected $configService;

    protected $contactService;

    /**
     * @param ScenarioService $service
     * @param MyScenarioService $myService
     * @param CampaignScenarioService $campaignScenarioService
     */
    public function __construct(
        ScenarioService $service,
        MyScenarioService $myService,
        CampaignScenarioService $campaignScenarioService,
        MailSendingHistoryService $mailSendingHistoryService,
        CampaignService $campaignService,
        UserService  $userService,
        ConfigService $configService,
        ContactService $contactService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->campaignScenarioService = $campaignScenarioService;
        $this->resourceCollectionClass = ScenarioResourceCollection::class;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->campaignService = $campaignService;
        $this->userService = $userService;
        $this->configService = $configService;
        $this->contactService = $contactService;
    }

    public function index(IndexRequest $request)
    {
        if ($request->get('sort') === 'number_credit' || $request->get('sort') === '-number_credit') {
            $models = $this->service->sortByNumberCreditOfScenario($request);
        } else {
            $models = $this->service->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function indexMy(IndexRequest $request)
    {
        if ($request->get('sort') === 'number_credit' || $request->get('sort') === '-number_credit') {
            $models = $this->myService->sortByNumberCreditOfMyScenario($request);
        } else {
            $models = $this->myService->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
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

        return $this->sendOkJsonResponse((['message' => __('messages.create_scenario_success')]));
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
    public function show($id)
    {
        $scenario = $this->service->findOrFailById($id);
        $nodes = $this->showCampaignScenarioByUuid($id);

        return $this->sendOkJsonResponse(['data' => [
            'name' => $scenario->name,
            'nodes' => $nodes
        ]]);
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyScenario($id)
    {
        $scenario = $this->myService->findMyScenarioByUuid($id);
        $nodes = $this->showCampaignScenarioByUuid($id);

        return $this->sendOkJsonResponse(['data' => [
            'name' => $scenario->name,
            'nodes' => $nodes
        ]]);
    }

    public function showCampaignScenarioByUuid($scenarioUuid)
    {
        $campaignsScenario = $this->campaignScenarioService->showCampaignScenarioByScenarioUuid($scenarioUuid);
        $depth = $this->campaignScenarioService->getMaxDepthOfCampaignScenarioByScenarioUuid($scenarioUuid);

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

        return $nodes;
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

        return $this->sendOkJsonResponse((['message' => __('messages.edit_scenario_success')]));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMyScenario($id)
    {
        $myScenario = $this->myService->findMyScenarioByUuid($id);
        $campaignScenarioRoot = $this->campaignScenarioService->getCampaignScenarioRootByScenarioUuid($myScenario->uuid);
        if ($campaignScenarioRoot && count($this->mailSendingHistoryService->showMailSendingByCampaignScenarioUuid($campaignScenarioRoot->uuid))) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["scenario_uuid" => __('messages.scenario_running')]]);
        }
        if ($campaignScenarioRoot){
            $this->campaignScenarioService->destroy($campaignScenarioRoot->uuid);
        }
        $this->myService->destroy($id);
        return $this->sendOkJsonResponse();
    }

    public function statusMyScenario(StatusMyScenarioRequest $request)
    {
        $scenario = $this->service->findOneById($request->get('scenario_uuid'));
        if ($request->get('status') === 'stopped') {
            //Hoàn trả credit dư
            CalculateCreditWhenStopScenarioEvent::dispatch($scenario);
            $this->service->update($scenario, [
                'status' => $request->get('status'),
                'last_stopped_at' => Carbon::now(),
            ]);

            SendNotificationSystemEvent::dispatch($scenario->user, Notification::SCENARIO_TYPE, Notification::STOP_ACTION, $scenario);
            return $this->sendOkJsonResponse();
        }

        $this->service->update($scenario, [
            'status' => $request->get('status')
        ]);

        $result = $this->checkAndSendScenario($scenario);

        if (!$result['status']) {
            $this->service->update($scenario, [
                'status' => 'stopped',
                'last_stopped_at' => Carbon::now(),
            ]);
            return $this->sendValidationFailedJsonResponse(['errors' => $result['messages']]);
        }

        return $this->sendOkJsonResponse(["message" => $result['messages']]);
    }

    public function checkAndSendScenario($scenario)
    {
        $campaignRootScenario = $this->campaignScenarioService->getCampaignScenarioRootByScenarioUuid($scenario->uuid);
        $campaign = $this->campaignService->checkActiveCampaignScenario($campaignRootScenario->campaign_uuid);

        $creditNumberSendEmail = $scenario->number_credit;
        if ($this->userService->checkCredit($creditNumberSendEmail, $campaign->user_uuid)) {
            SendNotificationSystemEvent::dispatch($scenario->user, Notification::SCENARIO_TYPE, Notification::START_ACTION, $scenario);
            SendByCampaignRootScenarioEvent::dispatch($campaign, $creditNumberSendEmail, $campaignRootScenario);
            return ['status' => true,
                'messages' => __('messages.send_scenario_success')];
        }
        return ['status' => false,
            'messages' => ['credit' => __('messages.credit_invalid')]];
    }
}
