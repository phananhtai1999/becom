<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\CreditPackageRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCreditPackageRequest;
use App\Http\Resources\CreditPackageResource;
use App\Http\Resources\CreditPackageResourceCollection;
use App\Services\CreditPackageService;

class CreditPackageController extends AbstractRestAPIController
{
    use RestStoreTrait, RestDestroyTrait, RestEditTrait, RestShowTrait, RestIndexTrait;

    public function __construct(CreditPackageService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = CreditPackageResourceCollection::class;
        $this->resourceClass = CreditPackageResource::class;
        $this->storeRequest = CreditPackageRequest::class;
        $this->editRequest = UpdateCreditPackageRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
