<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyStatusRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\UpdateMyStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\StatusResource;
use App\Http\Resources\StatusResourceCollection;
use App\Services\LanguageService;
use App\Services\MyStatusService;
use App\Services\StatusService;

class StatusController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @var
     */
    protected $languageService;

    /**
     * @param StatusService $service
     * @param MyStatusService $myService
     * @param LanguageService $languageService
     */
    public function __construct(
        StatusService   $service,
        MyStatusService $myService,
        LanguageService $languageService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->languageService = $languageService;
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

        //Allowed language
        if (!$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? null
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

        //Allowed language
        if ($request->name && !$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid', $model->user_uuid)
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MyStatusRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyStatus(MyStatusRequest $request)
    {
        //Allowed language
        if (!$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

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
        $model = $this->myService->showMyAndPublicStatus($id);

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

        //Allowed language
        if ($request->name && !$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

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
