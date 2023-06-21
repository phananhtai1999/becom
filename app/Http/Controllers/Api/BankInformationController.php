<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateBankInformationRequest;
use App\Http\Resources\BankInformationResource;
use App\Http\Resources\BankInformationResourceCollection;
use App\Services\BankInformationService;
use Illuminate\Http\Request;

class BankInformationController extends Controller
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait, RestStoreTrait;

    public function __construct(BankInformationService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = BankInformationResourceCollection::class;
        $this->resourceClass = BankInformationResource::class;
        $this->storeRequest = BankInformationRequest::class;
        $this->editRequest = UpdateBankInformationRequest::class;
        $this->indexRequest = IndexRequest::class;
    }
}
