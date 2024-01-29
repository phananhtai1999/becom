<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishSectionTemplateRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MySectionTemplateRequest;
use App\Http\Requests\SectionTemplateRequest;
use App\Http\Requests\UnpublishedSectionTemplateRequest;
use App\Http\Requests\UpdateMySectionTemplateRequest;
use App\Http\Requests\UpdateSectionTemplateRequest;
use App\Http\Requests\UpdateUnpublishedSectionTemplateRequest;
use App\Http\Resources\SectionTemplateResource;
use App\Http\Resources\SectionTemplateResourceCollection;
use App\Models\SectionTemplate;
use App\Services\MySectionTemplateService;
use App\Services\SectionTemplateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MySectionTemplateService
     */
    protected $myService;

    /**
     * @param SectionTemplateService $service
     * @param MySectionTemplateService $myService
     */
    public function __construct(
        SectionTemplateService $service,
        MySectionTemplateService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = SectionTemplateResourceCollection::class;
        $this->resourceClass = SectionTemplateResource::class;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = SectionTemplateRequest::class;
        $this->editRequest = UpdateSectionTemplateRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => SectionTemplate::PUBLISHED_PUBLISH_STATUS,
           'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
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

        $this->service->update($model, $request->except('user_uuid'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MySectionTemplateRequest $request
     * @return JsonResponse
     */
    public function storeMySectionTemplate(MySectionTemplateRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => SectionTemplate::PUBLISHED_PUBLISH_STATUS,
           'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'is_default' => false
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMySectionTemplate($id)
    {
        $model = $this->myService->showMySectionTemplateByUuid($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMySectionTemplateRequest $request
     * @return JsonResponse
     */
    public function editMySectionTemplate(UpdateMySectionTemplateRequest $request, $id)
    {
        $model = $this->myService->showMySectionTemplateByUuid($id);
        $this->service->update($model, $request->except(['user_uuid', 'is_default', 'publish_status']));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMySectionTemplate($id)
    {
        $this->myService->deleteMySectionTemplateByUuid($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUnpublishedSectionTemplate(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => SectionTemplate::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedSectionTemplate($id)
    {
        $model = $this->service->showSectionTemplateForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UnpublishedSectionTemplateRequest $request
     * @return JsonResponse
     */
    public function storeUnpublishedSectionTemplate(UnpublishedSectionTemplateRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
           'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedSectionTemplateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editUnpublishedSectionTemplate(UpdateUnpublishedSectionTemplateRequest $request, $id)
    {
        $model = $this->service->showSectionTemplateForEditorById($id);
        $this->service->update($model, $request->except(['user_uuid']));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishSectionTemplateRequest $request
     * @return JsonResponse
     */
    public function changeStatusSectionTemplate(AcceptPublishSectionTemplateRequest $request)
    {
        $sectionTemplateUuids = $request->section_templates;
        foreach ($sectionTemplateUuids as $sectionTemplateUuid)
        {
            $model = $this->service->findOneById($sectionTemplateUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == SectionTemplate::REJECT_PUBLISH_STATUS){
                $list_reason[] = [
                    'content' => $request->get('reject_reason'),
                    'created_at' => Carbon::now()
                ];
            }
            $this->service->update($model, [
                'publish_status' => $request->get('publish_status'),
                'reject_reason' => $list_reason
            ]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSectionTemplatesDefault(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['publish_status', SectionTemplate::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showSectionTemplateDefault($id)
    {
        $model = $this->service->showSectionTemplateDefaultById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function listMyAcceptedSectionTemplate(IndexRequest $request)
    {
        $models = $this->myService->getIsCanUseSectionTemplates($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function listAcceptedSectionTemplate(IndexRequest $request)
    {
        $models = $this->service->getIsCanUseSectionTemplates($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function getHeader($id)
    {
        $sectionHeader = $this->service->findOneWhereOrFail(['uuid' => $id]);
        $sectionHeader = $this->service->renderContentForHeader($sectionHeader);

        return $this->sendOkJsonResponse(['data' => $sectionHeader]);
    }

}
