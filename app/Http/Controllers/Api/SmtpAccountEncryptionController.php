<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\SmtpAccountEncryptionRequest;
use App\Http\Requests\UpdateSmtpAccountEncryptionRequest;
use App\Http\Resources\SmtpAccountEncryptionResource;
use App\Http\Resources\SmtpAccountEncryptionResourceCollection;
use App\Services\SmtpAccountEncryptionService;

class SmtpAccountEncryptionController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @param SmtpAccountEncryptionService $service
     */
    public function __construct(SmtpAccountEncryptionService $service)
    {
        $this->service = $service;
        $this->resourceClass = SmtpAccountEncryptionResource::class;
        $this->resourceCollectionClass = SmtpAccountEncryptionResourceCollection::class;
        $this->storeRequest = SmtpAccountEncryptionRequest::class;
        $this->editRequest = UpdateSmtpAccountEncryptionRequest::class;
    }

}
