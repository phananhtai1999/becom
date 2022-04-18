<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResourceCollection;
use App\Services\RoleService;

class RoleController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = RoleResourceCollection::class;
        $this->resourceClass = RoleResource::class;
        $this->storeRequest = RoleRequest::class;
        $this->editRequest = UpdateRoleRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
