<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\MailTemplateRequest;
use App\Http\Requests\MyMailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Http\Requests\UpdateMyMailTemplateRequest;
use App\Http\Resources\MailTemplateResourceCollection;
use App\Http\Resources\MailTemplateResource;
use App\Services\MailTemplateService;

class MailTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @param MailTemplateService $service
     */
    public function __construct(MailTemplateService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = MailTemplateResourceCollection::class;
        $this->resourceClass = MailTemplateResource::class;
        $this->storeRequest = MailTemplateRequest::class;
        $this->editRequest = UpdateMailTemplateRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyMailTemplate()
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->service->indexMyMailTemplate(request()->get('per_page', 15))
            )
        );
    }

    /**
     * @param MyMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyMailTemplate(MyMailTemplateRequest $request)
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
    public function showMyMailTemplate($id)
    {
        $model = $this->service->findMyMailTemplateByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyMailTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyMailTemplate(UpdateMyMailTemplateRequest $request, $id)
    {
        $model = $this->service->findMyMailTemplateByKeyOrAbort($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyMailTemplate($id)
    {
        $this->service->deleteMyMailTemplateByKey($id);

        return $this->sendOkJsonResponse();
    }
}
