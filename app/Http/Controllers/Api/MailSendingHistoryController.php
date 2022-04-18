<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\MailSendingHistoryRequest;
use App\Http\Requests\UpdateMailSendingHistoryRequest;
use App\Http\Resources\MailSendingHistoryResourceCollection;
use App\Http\Resources\MailSendingHistoryResource;
use App\Services\MailSendingHistoryService;

class MailSendingHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    public function __construct(MailSendingHistoryService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = MailSendingHistoryResourceCollection::class;
        $this->resourceClass = MailSendingHistoryResource::class;
        $this->storeRequest = MailSendingHistoryRequest::class;
        $this->editRequest = UpdateMailSendingHistoryRequest::class;
    }
}
