<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\CampaignRequest;
use App\Http\Requests\IncrementCampaignTrackingRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CampaignCollection;
use App\Http\Resources\CampaignDailyTrackingResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignTrackingResource;
use App\Services\CampaignDailyTrackingService;
use App\Services\CampaignService;
use App\Services\CampaignTrackingService;

class CampaignController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    protected $campaignTrackingService;
    protected $campaignDailyTrackingService;


    public function __construct(CampaignService $service,
                                CampaignTrackingService $campaignTrackingService,
                                CampaignDailyTrackingService $campaignDailyTrackingService)
    {
        $this->service = $service;
        $this->resourceCollectionClass = CampaignCollection::class;
        $this->resourceClass = CampaignResource::class;
        $this->storeRequest = CampaignRequest::class;
        $this->editRequest = UpdateCampaignRequest::class;
        $this->campaignTrackingService = $campaignTrackingService;
        $this->campaignDailyTrackingService = $campaignDailyTrackingService;
    }

    /**
     * @param IncrementCampaignTrackingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function incrementCampaignTrackingTotalOpen(IncrementCampaignTrackingRequest $request)
    {
        $campaignTrackingData = $this->service->resourceToData(
            CampaignTrackingResource::class,
            $this->campaignTrackingService->incrementTotalOpenByCampaignUuid($request->get('campaign_uuid'))
        );

        $campaignDailyTrackingData = $this->service->resourceToData(
            CampaignDailyTrackingResource::class,
            $this->campaignDailyTrackingService->incrementTotalOpenByCampaignUuid($request->get('campaign_uuid'))
        );

        return $this->sendOkJsonResponse([
            'data' => [
                'campaignTracking' => $campaignTrackingData['data'],
                'campaignDailyTracking' => $campaignDailyTrackingData['data'],
            ]
        ]);
    }
}
