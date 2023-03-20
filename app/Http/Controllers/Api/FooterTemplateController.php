<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\FooterTemplateRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyFooterTemplateRequest;
use App\Http\Requests\UpdateFooterTemplateRequest;
use App\Http\Requests\UpdateMyFooterTemplateRequest;
use App\Http\Resources\FooterTemplateResource;
use App\Http\Resources\FooterTemplateResourceCollection;
use App\Models\FooterTemplate;
use App\Services\FooterTemplateService;
use App\Services\MyFooterTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FooterTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait;

    protected $myService;

    public function __construct(
        FooterTemplateService $service,
        MyFooterTemplateService $myService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = FooterTemplateResourceCollection::class;
        $this->resourceClass = FooterTemplateResource::class;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = FooterTemplateRequest::class;
        $this->editRequest = UpdateFooterTemplateRequest::class;
        $this->myService = $myService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);
        $model = $this->service->create(array_merge($request->except('active_by_uuid'), [
            'user_uuid' => auth()->user()->getKey(),
            'is_default' => false,
            'publish_status' => FooterTemplate::PUBLISHED_PUBLISH_STATUS
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        if ($request->get('is_default') && $model->is_default != $request->get('is_default')) {
            if ($model->user_uuid != auth()->user()->getKey()) {
                return $this->sendValidationFailedJsonResponse();
            }
                $this->service->changeIsDefaultFooterTemplateByType($model->type);
        }

        $this->service->update($model, $request->except(['user_uuid', 'publish_status', 'active_by_uuid']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $model = $this->service->findOrFailById($id);
        if (!$model->is_default) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }
        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse|void
     */
    public function indexMyFooterTemplate(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    public function storeMyFooterTemplate(MyFooterTemplateRequest $request)
    {
        $model = $this->service->create(array_merge($request->except('active_by_uuid'), [
            'user_uuid' => auth()->user()->getKey(),
            'is_default' => false,
            'publish_status' => FooterTemplate::PUBLISHED_PUBLISH_STATUS
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );

    }

    public function showMyFooterTemplate($id)
    {
        $model = $this->myService->showMyFooterTemplate($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editMyFooterTemplate(UpdateMyFooterTemplateRequest $request, $id)
    {
        $model = $this->myService->showMyFooterTemplate($id);

        if ($request->get('active_by_uuid') && $model->active_by_uuid != $request->get('active_by_uuid')) {
            $this->service->changeActiveByFooterTemplateByType($model->type);
        }
        $this->service->update($model, $request->except(['user_uuid', 'publish_status', 'is_default']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function destroyMyFooterTemplate($id)
    {
        $model = $this->myService->showMyFooterTemplate($id);
        if (!$model->active_by_uuid) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }
        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);
    }
}
