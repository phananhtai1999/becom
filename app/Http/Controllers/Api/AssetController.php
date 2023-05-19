<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetResourceCollection;
use App\Services\AssetService;

class AssetController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait, RestStoreTrait;

    public function __construct(AssetService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = AssetResourceCollection::class;
        $this->resourceClass = AssetResource::class;
        $this->storeRequest = AssetRequest::class;
        $this->editRequest = UpdateAssetRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
