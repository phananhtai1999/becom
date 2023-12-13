<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LoadAnalyticDataRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResourceCollection;
use App\Http\Resources\OrderResource;
use App\Services\MyOrderService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait, RestIndexMyTrait;

    /**
     * @var MyOrderService
     */
    protected $myService;

    /**
     * @param OrderService $service
     * @param MyOrderService $myService
     */
    public function __construct(
        OrderService   $service,
        MyOrderService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = OrderResourceCollection::class;
        $this->resourceClass = OrderResource::class;
        $this->storeRequest = OrderRequest::class;
        $this->editRequest = UpdateOrderRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyOrder($id)
    {
        $model = $this->myService->showMyOrder($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function editMyOrder($id)
    {
        $request = app($this->editRequest);
        $model = $this->myService->showMyOrder($id);
        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyOrder($id)
    {
        $this->myService->deleteMyOrderById($id);

        return $this->sendOkJsonResponse();
    }
}
