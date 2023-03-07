<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyPositionRequest;
use App\Http\Requests\MyStatusRequest;
use App\Http\Requests\PositionRequest;
use App\Http\Requests\UpdateMyPositionRequest;
use App\Http\Requests\UpdateMyStatusRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\PostionResource;
use App\Http\Resources\PostionResourceCollection;
use App\Services\MyPositionService;
use App\Services\MyStatusService;
use App\Services\PositionService;

class PositionController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @param PositionService $service
     * @param MyPositionService $myService
     */
    public function __construct(
        PositionService   $service,
        MyPositionService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = PostionResourceCollection::class;
        $this->resourceClass = PostionResource::class;
        $this->storeRequest = PositionRequest::class;
        $this->editRequest = UpdatePositionRequest::class;
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
    public function indexMyPosition(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    /**
     * @param MyPositionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyPosition(MyPositionRequest $request)
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
    public function showMyPosition($id)
    {
        $model = $this->myService->showMyPosition($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyPositionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyPosition(UpdateMyPositionRequest $request, $id)
    {
        $model = $this->myService->showMyPosition($id);

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
    public function destroyMyPosition($id)
    {
        $this->myService->deleteMyPosition($id);

        return $this->sendOkJsonResponse();
    }
}