<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Requests\RegisterPartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PartnerResourceCollection;
use App\Models\Partner;
use App\Services\PartnerService;

class PartnerController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    public function __construct(
        PartnerService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerResourceCollection::class;
        $this->resourceClass = PartnerResource::class;
        $this->storeRequest = PartnerRequest::class;
        $this->editRequest = UpdatePartnerRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => Partner::PUBLISHED_PUBLISH_STATUS
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }

    public function registerPartner(RegisterPartnerRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => Partner::PENDING_PUBLISH_STATUS
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }
}
