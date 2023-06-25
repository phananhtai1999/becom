<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PayoutInformationRequest;
use App\Http\Requests\UpdatePayoutInformationRequest;
use App\Http\Resources\PayoutMethodResource;
use App\Http\Resources\PayoutMethodResourceCollection;
use App\Services\PayoutInformationService;
use Illuminate\Http\Request;

class PayoutMethodController extends Controller
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait, RestStoreTrait;

    public function __construct(PayoutInformationService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = PayoutMethodResourceCollection::class;
        $this->resourceClass = PayoutMethodResource::class;
        $this->storeRequest = PayoutInformationRequest::class;
        $this->editRequest = UpdatePayoutInformationRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function store(PayoutInformationRequest $request)
    {
        if (!$this->service->getDefault()) {
            $model = $this->service->create(array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
                'is_default' => true
            ]));
        } else {
            $model = $this->service->create(array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]));
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function myIndex()
    {
        $models = $this->service->getMyPayoutInformation();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function setDefault($id) {
        $default = $this->service->getDefault();
        if ($default) {
            $this->service->update($default, ['is_default' => false]);
        }
        $payoutInformation = $this->service->findOrFailById($id);
        $this->service->update($payoutInformation, ['is_default' => true]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $payoutInformation)
        );
    }
}
