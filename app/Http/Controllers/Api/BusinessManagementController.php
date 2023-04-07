<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\BusinessManagementRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyBusinessManagementRequest;
use App\Http\Requests\UpdateBusinessManagementRequest;
use App\Http\Requests\UpdateMyBusinessManagementRequest;
use App\Http\Resources\BusinessManagementResource;
use App\Http\Resources\BusinessManagementResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Services\BusinessManagementService;
use App\Services\MyBusinessManagementService;

class BusinessManagementController extends AbstractRestAPIController
{
    use RestIndexTrait, RestDestroyTrait, RestShowTrait;

    /**
     * @var MyBusinessManagementService
     */
    protected $myService;

    /**
     * @param BusinessManagementService $service
     * @param MyBusinessManagementService $myService
     */
    public function __construct(
        BusinessManagementService   $service,
        MyBusinessManagementService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = BusinessManagementResourceCollection::class;
        $this->resourceClass = BusinessManagementResource::class;
        $this->storeRequest = BusinessManagementRequest::class;
        $this->editRequest = UpdateBusinessManagementRequest::class;
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

        // User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser($request->get('owner_uuid') ?? auth()->user()->getKey());
        if ($businessManagement) {
            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => $request->get('owner_uuid') ?? auth()->user()->getKey(),
        ]));
        $model->businessCategories()->attach($request->get('business_categories', []));

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

        //User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser($request->get('owner_uuid'));
        if ($businessManagement && $model->owner_uuid != $request->get('owner_uuid')) {

            return $this->sendValidationFailedJsonResponse();
        }
        $this->service->update($model, $request->all());
        $model->businessCategories()->sync($request->business_categories ?? $model->business_categories);

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
    public function indexMyBusinessManagements(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    /**
     * @param MyBusinessManagementRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyBusinessManagement(MyBusinessManagementRequest $request)
    {
        // User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser(auth()->user()->getKey());
        if ($businessManagement) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));
        $model->businessCategories()->attach($request->get('business_categories', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyBusinessManagement($id)
    {
        $model = $this->myService->showMyBusinessManagement($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyBusinessManagementRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyBusinessManagement(UpdateMyBusinessManagementRequest $request, $id)
    {
        $model = $this->myService->showMyBusinessManagement($id);

        $this->service->update($model, array_merge($request->all(), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));
        $model->businessCategories()->sync($request->business_categories ?? $model->business_categories);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyBusinessManagement($id)
    {
        $this->myService->deleteMyBusinessManagement($id);

        return $this->sendOkJsonResponse();
    }
}
