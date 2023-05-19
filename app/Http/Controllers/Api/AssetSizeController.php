<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\AssetSizeRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetSizeRequest;
use App\Http\Resources\AssetSizeResource;
use App\Http\Resources\AssetSizeResourceCollection;
use App\Services\AssetSizeService;

class AssetSizeController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait, RestStoreTrait;

    public function __construct(AssetSizeService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = AssetSizeResourceCollection::class;
        $this->resourceClass = AssetSizeResource::class;
        $this->storeRequest = AssetSizeRequest::class;
        $this->editRequest = UpdateAssetSizeRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
