<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResourceCollection;
use App\Http\Resources\PaymentMethodResource;
use App\Services\PaymentMethodService;

class PaymentMethodController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    /**
     * @param PaymentMethodService $service
     */
    public function __construct(PaymentMethodService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = PaymentMethodResourceCollection::class;
        $this->resourceClass = PaymentMethodResource::class;
        $this->storeRequest = PaymentMethodRequest::class;
        $this->editRequest = UpdatePaymentMethodRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
