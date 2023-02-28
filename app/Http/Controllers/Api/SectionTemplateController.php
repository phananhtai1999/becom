<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishSectionTemplateRequest;
use App\Http\Requests\AcceptPublishWebsitePageRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsitePageRequest;
use App\Http\Requests\UpdateMyWebsitePageRequest;
use App\Http\Requests\UpdateWebsitePageRequest;
use App\Http\Requests\WebsitePageRequest;
use App\Http\Resources\SectionTemplateResource;
use App\Http\Resources\SectionTemplateResourceCollection;
use App\Models\SectionTemplate;
use App\Models\WebsitePage;
use App\Services\MySectionTemplateService;
use App\Services\SectionTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

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
        $this->storeRequest = WebsitePageRequest::class;
        $this->editRequest = UpdateWebsitePageRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getKey()
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
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMySectionTemplate(IndexRequest $request)
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
     * @param MyWebsitePageRequest $request
     * @return JsonResponse
     */
    public function storeMySectionTemplate(MyWebsitePageRequest $request)
    {
        $model = $this->service->create(array_merge($request->except(['user_uuid', 'publish_status']), [
            'publish_status' => SectionTemplate::PENDING_PUBLISH_STATUS,
            'user_uuid' => auth()->user()->getkey(),
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
     * @param UpdateMyWebsitePageRequest $request
     * @return JsonResponse
     */
    public function editMySectionTemplate(UpdateMyWebsitePageRequest $request, $id)
    {
        $model = $this->myService->showMySectionTemplateByUuid($id);
        $this->service->update($model, $request->except(['user_uuid', 'publish_status']));

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
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->service->indexSectionTemplateByPublishStatus(
                    SectionTemplate::PENDING_PUBLISH_STATUS,
                    $request->get('per_page', '15'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                    $request->get('page', '1'),
                    $request->get('search'),
                    $request->get('search_by'),
                )
            )
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedSectionTemplate($id)
    {
        $model = $this->service->findSectionTemplateByKeyAndPublishStatus(SectionTemplate::PENDING_PUBLISH_STATUS, $id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishSectionTemplateRequest $request
     * @return JsonResponse
     */
    public function acceptPublishSectionTemplate(AcceptPublishSectionTemplateRequest $request)
    {
        $sectionTemplateUuids = $request->section_templates;
        foreach ($sectionTemplateUuids as $sectionTemplateUuid)
        {
            $model = $this->service->findOneById($sectionTemplateUuid);
            $this->service->update($model, ['publish_status' => SectionTemplate::PUBLISHED_PUBLISH_STATUS]);
        }

        return $this->sendOkJsonResponse();
    }

}
