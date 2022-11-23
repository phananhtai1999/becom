<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
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
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var MyOrderService
     */
    protected $myService;

    /**
     * @param OrderService $service
     * @param MyOrderService $myService
     */
    public function __construct(
        OrderService $service,
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
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyOrder(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                )
            )
        );
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
            'user_uuid' => auth()->user()->getkey(),
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