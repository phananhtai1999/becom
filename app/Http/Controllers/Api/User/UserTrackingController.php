<?php

namespace App\Http\Controllers\Api\User;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateUserTrackingRequest;
use App\Http\Resources\UserTrackingResource;
use App\Http\Resources\UserTrackingResourceCollection;
use App\Services\UserTrackingService;
use Illuminate\Http\Request;

class UserTrackingController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestDestroyTrait;

    /**
     * @param UserTrackingService $service
     */
    public function __construct(
        UserTrackingService  $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserTrackingResourceCollection::class;
        $this->resourceClass = UserTrackingResource::class;
        $this->editRequest = UpdateUserTrackingRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
