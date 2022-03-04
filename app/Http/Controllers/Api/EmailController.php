<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Resources\EmailCollection;
use App\Http\Resources\EmailResource;
use App\Services\EmailService;

class EmailController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    public function __construct(EmailService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = EmailCollection::class;
        $this->resourceClass = EmailResource::class;
        $this->storeRequest = EmailRequest::class;
        $this->editRequest = UpdateEmailRequest::class;
    }
}
