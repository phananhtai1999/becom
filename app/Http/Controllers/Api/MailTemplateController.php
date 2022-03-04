<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\MailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Http\Resources\MailTemplateCollection;
use App\Http\Resources\MailTemplateResource;
use App\Services\MailTemplateService;

class MailTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    public function __construct(MailTemplateService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = MailTemplateCollection::class;
        $this->resourceClass = MailTemplateResource::class;
        $this->storeRequest = MailTemplateRequest::class;
        $this->editRequest = UpdateMailTemplateRequest::class;
    }
}
