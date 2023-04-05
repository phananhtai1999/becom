<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerTrackingRequest;
use App\Http\Requests\UpdatePartnerTrackingRequest;
use App\Http\Resources\PartnerTrackingResource;
use App\Http\Resources\PartnerTrackingResourceCollection;
use App\Services\PartnerTrackingService;

class PartnerTrackingController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestStoreTrait,RestEditTrait, RestDestroyTrait;

    /**
     * @param PartnerTrackingService $service
     */
    public function __construct(
        PartnerTrackingService  $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerTrackingResourceCollection::class;
        $this->resourceClass = PartnerTrackingResource::class;
        $this->storeRequest = PartnerTrackingRequest::class;
        $this->editRequest = UpdatePartnerTrackingRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
