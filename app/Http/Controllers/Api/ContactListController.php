<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ContactListRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateContactListRequest;
use App\Http\Resources\ContactListResource;
use App\Http\Resources\ContactListResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Services\ContactListService;

class ContactListController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(ContactListService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = ContactListResourceCollection::class;
        $this->resourceClass = ContactListResource::class;
        $this->storeRequest = ContactListRequest::class;
        $this->editRequest = UpdateContactListRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
