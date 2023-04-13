<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\BillingAddressRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateBillingAddressRequest;
use App\Http\Resources\BillingAddressResource;
use App\Http\Resources\BillingAddressResourceCollection;
use App\Services\BillingAddressService;
use Illuminate\Http\JsonResponse;

class BillingAddressController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait;

    public function __construct(BillingAddressService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = BillingAddressResourceCollection::class;
        $this->resourceClass = BillingAddressResource::class;
        $this->storeRequest = BillingAddressRequest::class;
        $this->editRequest = UpdateBillingAddressRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param BillingAddressRequest $request
     * @return JsonResponse
     */
    public function store(BillingAddressRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateBillingAddressRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(UpdateBillingAddressRequest $request, $id)
    {
        $billingAddress = $this->service->findOrFailById($id);
        if ($billingAddress->billing_address != $request->get('billing_address')) {
            $this->service->destroy($billingAddress->uuid);
            $billingAddress = $this->service->create(array_merge($billingAddress->toArray(), [
                'user_uuid' => auth()->user()->getkey(),
            ], $request->all()));
        } else {
            $this->service->update($billingAddress, $request->all());
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $billingAddress)
        );
    }

    public function myIndex() {
        $models = $this->service->findAllWhere(['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
