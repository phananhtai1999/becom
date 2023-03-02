<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Services\GroupService;

class GroupController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestStoreTrait, RestDestroyTrait;

    /**
     * @param GroupService $service
     */
    public function __construct(GroupService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = GroupResourceCollection::class;
        $this->resourceClass = GroupResource::class;
        $this->storeRequest = GroupRequest::class;
        $this->editRequest = UpdateGroupRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
