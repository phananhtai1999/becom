<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\DomainVerificationResource;
use App\Http\Resources\DomainVerificationResourceCollection;
use App\Services\DomainVerificationService;
use App\Services\MyDomainVerificationService;

class DomainVerificationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyDomainVerificationService
     */
    protected $myService;

    /**
     * @param DomainVerificationService $service
     * @param MyDomainVerificationService $myService
     */
    public function __construct(
        DomainVerificationService $service,
        MyDomainVerificationService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceClass = DomainVerificationResource::class;
        $this->resourceCollectionClass = DomainVerificationResourceCollection::class;
        $this->indexRequest = IndexRequest::class;
    }
}
