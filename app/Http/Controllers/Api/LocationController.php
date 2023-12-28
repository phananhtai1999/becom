<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestMyDestroyTrait;
use App\Http\Controllers\Traits\RestMyEditTrait;
use App\Http\Controllers\Traits\RestMyShowTrait;
use App\Http\Controllers\Traits\RestMyStoreTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\RemoveTeamFromLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\MyLocationRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationResourceCollection;
use App\Services\BusinessManagementService;
use App\Services\LocationService;
use App\Services\SendProjectService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;

class LocationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestStoreTrait, RestDestroyTrait,
        RestIndexMyTrait, RestMyStoreTrait, RestMyShowTrait, RestMyDestroyTrait, RestMyEditTrait;

    /**
     * @param LocationService $service
     * @param TeamService $teamService
     */
    public function __construct(
        LocationService $service,
        TeamService $teamService,
        SendProjectService $sendProjectService,
        BusinessManagementService $businessManagementService
    )
    {
        $this->service = $service;
        $this->teamService = $teamService;
        $this->myService = $service;
        $this->sendProjectService = $sendProjectService;
        $this->businessManagementService = $businessManagementService;
        $this->resourceCollectionClass = LocationResourceCollection::class;
        $this->resourceClass = LocationResource::class;
        $this->storeRequest = LocationRequest::class;
        $this->storeMyRequest = MyLocationRequest::class;
        $this->editRequest = UpdateLocationRequest::class;
        $this->editMyRequest = UpdateLocationRequest::class;
        $this->indexRequest = IndexRequest::class;
    }


    /**
     * @param RemoveTeamFromLocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTeam(RemoveTeamFromLocationRequest $request)
    {
        foreach ($request->get('team_uuids') as $teamUuid) {
            $team = $this->teamService->findOneWhere(['uuid' => $teamUuid, 'location_uuid' => $request->get('location_uuid')]);
            if ($team) {
                $team->update(['location_uuid' => null]);
            }
        }

        return $this->sendOkJsonResponse();
    }

    public function getAssignableForProject(IndexRequest $request, $id) {
        $sendProject = $this->sendProjectService->findOrFailById($id);
        $locations = $this->service->getLocationsAssignable($sendProject->business->uuid, $id, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $locations)
        );
    }

    /**
     * @return JsonResponse
     */
    public function storeMy()
    {
        $request = app($this->storeMyRequest);

        $business = $this->getBusiness();
        if (!$business) {
            return $this->sendJsonResponse(false, 'Does not have business', [], 403);
        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
            'business_uuid' => $business->uuid
        ]));

        return $this->sendCreatedJsonResponse(
            $this->myService->resourceToData($this->resourceClass, $model)
        );
    }
}
