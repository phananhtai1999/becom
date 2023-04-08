<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\DomainRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\UpdateMyDomainRequest;
use App\Http\Resources\DomainResource;
use App\Http\Resources\DomainResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Services\DomainService;
use App\Services\MyDomainService;

class DomainController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyDomainService
     */
    protected $myService;

    /**
     * @param DomainService $service
     * @param MyDomainService $myService
     */
    public function __construct(
        DomainService   $service,
        MyDomainService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = DomainResourceCollection::class;
        $this->resourceClass = DomainResource::class;
        $this->storeRequest = DomainRequest::class;
        $this->editRequest = UpdateDomainRequest::class;
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
            'owner_uuid' => $request->get('owner_uuid') ?? auth()->user()->getKey(),
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
            'owner_uuid' => $request->get('owner_uuid') ?? auth()->user()->getKey(),
            'business_uuid' => $request->get('business_uuid') ?? null
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MyDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyDomain(MyDomainRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyDomain($id)
    {
        $model = $this->myService->showMyDomain($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyDomainRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyDomain(UpdateMyDomainRequest $request, $id)
    {
        $model = $this->myService->showMyDomain($id);

        $this->service->update($model, array_merge($request->all(), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyDomain($id)
    {
        $this->myService->deleteMyDomain($id);

        return $this->sendOkJsonResponse();
    }
}
