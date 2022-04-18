<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\MyEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateMyEmailRequest;
use App\Http\Resources\EmailResourceCollection;
use App\Http\Resources\EmailResource;
use App\Services\EmailService;

class EmailController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @param EmailService $service
     */
    public function __construct(EmailService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = EmailResourceCollection::class;
        $this->resourceClass = EmailResource::class;
        $this->storeRequest = EmailRequest::class;
        $this->editRequest = UpdateEmailRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyEmail()
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->service->indexMyEmail(request()->get('per_page', 15))
            )
        );
    }

    /**
     * @param MyEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyEmail(MyEmailRequest $request)
    {
        $model = $this->service->create($request->all());

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyEmail($id)
    {
        $model = $this->service->findMyEmailByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyEmailRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyEmail(UpdateMyEmailRequest $request, $id)
    {
        $model = $this->service->findMyEmailByKeyOrAbort($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyEmail($id)
    {
        $this->service->deleteMyEmailByKey($id);

        return $this->sendOkJsonResponse();
    }
}
