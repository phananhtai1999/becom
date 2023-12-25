<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyByUserIdAndAppIdTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditByUserIdAndAppIdTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexByUserIdAndAppIdTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowByUserIdAndAppIdTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreByUserIdAndAppIdTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\RemoveTeamFromLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\MyLocationRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationResourceCollection;
use App\Services\LocationService;
use App\Services\TeamService;

class LocationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestStoreTrait, RestDestroyTrait,
        RestIndexByUserIdAndAppIdTrait, RestStoreByUserIdAndAppIdTrait, RestShowByUserIdAndAppIdTrait, RestDestroyByUserIdAndAppIdTrait, RestEditByUserIdAndAppIdTrait;

    /**
     * @param LocationService $service
     * @param TeamService $teamService
     */
    public function __construct(LocationService $service, TeamService $teamService)
    {
        $this->service = $service;
        $this->teamService = $teamService;
        $this->myService = $service;
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
}
