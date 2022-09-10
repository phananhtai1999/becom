<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MailTemplateRequest;
use App\Http\Requests\MyMailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Http\Requests\UpdateMyMailTemplateRequest;
use App\Http\Resources\MailTemplateResourceCollection;
use App\Http\Resources\MailTemplateResource;
use App\Services\MailTemplateService;
use App\Services\MyMailTemplateService;

class MailTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param MailTemplateService $service
     * @param MyMailTemplateService $myService
     */
    public function __construct(
        MailTemplateService $service,
        MyMailTemplateService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = MailTemplateResourceCollection::class;
        $this->resourceClass = MailTemplateResource::class;
        $this->storeRequest = MailTemplateRequest::class;
        $this->editRequest = UpdateMailTemplateRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyMailTemplate(IndexRequest $request)
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
     * @param MyMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyMailTemplate(MyMailTemplateRequest $request)
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
    public function showMyMailTemplate($id)
    {
        $model = $this->myService->findMyMailTemplateByKeyOrAbort($id);

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
        $model = $this->myService->findMyMailTemplateByKeyOrAbort($id);

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
    public function destroyMyMailTemplate($id)
    {
        $this->myService->deleteMyMailTemplateByKey($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMailTemplatesDefault(IndexRequest $request)
    {
        $models = $this->service->getMailTemplateDefaultWithPagination(
            $request->get('per_page', '15'),
            $request->get('page', '1'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
