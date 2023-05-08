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
use App\Services\MyUserTrackingService;
use App\Services\UserTrackingService;

class UserTrackingController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestDestroyTrait, RestIndexMyTrait;

    protected $myService;

    /**
     * @param UserTrackingService $service
     */
    public function __construct(
        UserTrackingService  $service,
        MyUserTrackingService $myService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserTrackingResourceCollection::class;
        $this->resourceClass = UserTrackingResource::class;
        $this->editRequest = UpdateUserTrackingRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->myService = $myService;
    }
}
