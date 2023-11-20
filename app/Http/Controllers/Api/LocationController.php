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
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\MyLocationRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationResourceCollection;
use App\Services\LocationService;

class LocationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestStoreTrait, RestDestroyTrait,
        RestIndexMyTrait, RestMyStoreTrait, RestMyShowTrait, RestMyDestroyTrait, RestMyEditTrait;

    /**
     * @param LocationService $service
     */
    public function __construct(LocationService $service)
    {
        $this->service = $service;
        $this->myService = $service;
        $this->resourceCollectionClass = LocationResourceCollection::class;
        $this->resourceClass = LocationResource::class;
        $this->storeRequest = LocationRequest::class;
        $this->storeMyRequest = MyLocationRequest::class;
        $this->editRequest = UpdateLocationRequest::class;
        $this->editMyRequest = UpdateLocationRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
