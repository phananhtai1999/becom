<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyCompanyRequest;
use App\Http\Requests\MyRemindRequest;
use App\Http\Requests\RemindRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\UpdateMyCompanyRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\UpdateMyRemindRequest;
use App\Http\Requests\UpdateRemindRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyResourceCollection;
use App\Http\Resources\RemindResource;
use App\Http\Resources\RemindResourceCollection;
use App\Services\CompanyService;
use App\Services\MyCompanyService;
use App\Services\MyRemindService;
use App\Services\MyStatusService;
use App\Services\RemindService;

class RemindController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @param RemindService $service
     * @param MyRemindService $myService
     */
    public function __construct(
        RemindService   $service,
        MyRemindService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = RemindResourceCollection::class;
        $this->resourceClass = RemindResource::class;
        $this->storeRequest = RemindRequest::class;
        $this->editRequest = UpdateRemindRequest::class;
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
    public function indexMyRemind(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    /**
     * @param MyRemindRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyRemind(MyRemindRequest $request)
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
    public function showMyRemind($id)
    {
        $model = $this->myService->showMyRemind($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyRemindRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyRemind(UpdateMyRemindRequest $request, $id)
    {
        $model = $this->myService->showMyRemind($id);

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
    public function destroyMyRemind($id)
    {
        $this->myService->deleteMyRemind($id);

        return $this->sendOkJsonResponse();
    }
}
