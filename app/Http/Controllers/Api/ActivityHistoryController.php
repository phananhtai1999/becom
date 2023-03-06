<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\ActivityHistoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateActivityHistoryRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\ActivityHistoryResource;
use App\Http\Resources\ActivityHistoryResourceCollection;
use App\Services\ActivityHistoryService;

class ActivityHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @param ActivityHistoryService $service
     */
    public function __construct(
        ActivityHistoryService   $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ActivityHistoryResourceCollection::class;
        $this->resourceClass = ActivityHistoryResource::class;
        $this->storeRequest = ActivityHistoryRequest::class;
        $this->editRequest = UpdateActivityHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
