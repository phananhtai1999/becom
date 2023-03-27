<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CountryResourceCollection;
use App\Services\CountryService;

class CountryController extends AbstractRestAPIController
{
    use RestStoreTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait;

    public function __construct(
        CountryService   $service,
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = CountryResourceCollection::class;
        $this->resourceClass = CountryResource::class;
        $this->storeRequest = CountryRequest::class;
        $this->editRequest = UpdateCountryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
