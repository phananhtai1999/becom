<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\AssetGroupRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetGroupRequest;
use App\Http\Resources\AssetGroupResource;
use App\Http\Resources\AssetGroupResourceCollection;
use App\Services\AssetGroupService;

class AssetGroupController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait, RestStoreTrait;

    public function __construct(AssetGroupService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = AssetGroupResourceCollection::class;
        $this->resourceClass = AssetGroupResource::class;
        $this->storeRequest = AssetGroupRequest::class;
        $this->editRequest = UpdateAssetGroupRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
