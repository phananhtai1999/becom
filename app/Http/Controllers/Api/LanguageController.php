<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\LanguageResourceCollection;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestDestroyTrait, RestStoreTrait;

    public function __construct(LanguageService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = LanguageResourceCollection::class;
        $this->resourceClass = LanguageResource::class;
        $this->storeRequest = LanguageRequest::class;
        $this->editRequest = UpdateLanguageRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
