<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
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
        $default = $this->service->defaultBillingAddress();
        if (!$default) {
            $model = $this->service->create(array_merge($request->all(), [
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
                'is_default' => true
            ]));
        } else {
            $model = $this->service->create(array_merge($request->all(), [
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]));
        }

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
        $billingAddress = $this->service->findOneWhereOrFailByUserUuidAndAppId($id);
        if ($billingAddress->billing_address != $request->get('billing_address')) {
            $this->service->destroyByUserIdAndAppId($billingAddress->uuid);
            $billingAddress = $this->service->create(array_merge($billingAddress->toArray(), [
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ], $request->all()));
        } else {
            $this->service->update($billingAddress, $request->all());
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $billingAddress)
        );
    }

    public function myIndex()
    {
        $models = $this->service->findAllWhere([
            ['user_uuid' => auth()->user()],
            ['app_id' => auth()->appId()]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function setDefault($id) {
        $default = $this->service->defaultBillingAddress();
        if ($default) {
            $this->service->update($default, ['is_default' => false]);
        }
        $billingAddress = $this->service->findOrFailById($id);
        $this->service->update($billingAddress, ['is_default' => true]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $billingAddress)
        );
    }
}
