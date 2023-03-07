<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyStatusRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\UpdateMyStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\StatusResource;
use App\Http\Resources\StatusResourceCollection;
use App\Services\MyStatusService;
use App\Services\StatusService;

class StatusController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @param StatusService $service
     * @param MyStatusService $myService
     */
    public function __construct(
        StatusService   $service,
        MyStatusService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = StatusResourceCollection::class;
        $this->resourceClass = StatusResource::class;
        $this->storeRequest = StatusRequest::class;
        $this->editRequest = UpdateStatusRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->user()->getKey()
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->user()->getKey()
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyStatus(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    /**
     * @param MyStatusRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyStatus(MyStatusRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyStatus($id)
    {
        $model = $this->myService->showMyStatus($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyStatusRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyStatus(UpdateMyStatusRequest $request, $id)
    {
        $model = $this->myService->showMyStatus($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyStatus($id)
    {
        $this->myService->deleteMyStatus($id);

        return $this->sendOkJsonResponse();
    }
}
