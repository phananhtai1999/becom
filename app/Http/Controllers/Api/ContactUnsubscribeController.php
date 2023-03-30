<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\ContactUnsubscribeResource;
use App\Http\Resources\ContactUnsubscribeResourceCollection;
use App\Services\ContactUnsubscribeService;

class ContactUnsubscribeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    public function __construct(
        ContactUnsubscribeService  $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ContactUnsubscribeResourceCollection::class;
        $this->resourceClass = ContactUnsubscribeResource::class;
        $this->indexRequest = IndexRequest::class;
    }

}
