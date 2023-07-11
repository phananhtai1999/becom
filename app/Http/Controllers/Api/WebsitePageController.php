<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishWebsitePageRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsitePageRequest;
use App\Http\Requests\UnpublishedWebsitePageRequest;
use App\Http\Requests\UpdateMyWebsitePageRequest;
use App\Http\Requests\UpdateUnpublishedWebsitePageRequest;
use App\Http\Requests\UpdateWebsitePageRequest;
use App\Http\Requests\WebsitePageRequest;
use App\Http\Resources\WebsitePageResource;
use App\Http\Resources\WebsitePageResourceCollection;
use App\Models\WebsitePage;
use App\Services\MyWebsitePageService;
use App\Services\WebsitePageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsitePageController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyWebsitePageService
     */
    protected $myService;

    /**
     * @param WebsitePageService $service
     * @param MyWebsitePageService $myService
     */
    public function __construct(
        WebsitePageService $service,
        MyWebsitePageService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = WebsitePageResourceCollection::class;
        $this->resourceClass = WebsitePageResource::class;
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
            'publish_status' => WebsitePage::PUBLISHED_PUBLISH_STATUS,
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
     * @param MyWebsitePageRequest $request
     * @return JsonResponse
     */
    public function storeMyWebsitePage(MyWebsitePageRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
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
    public function showMyWebsitePage($id)
    {
        $model = $this->myService->showMyWebsitePageByUuid($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyWebsitePageRequest $request
     * @return JsonResponse
     */
    public function editMyWebsitePage(UpdateMyWebsitePageRequest $request, $id)
    {
        $model = $this->myService->showMyWebsitePageByUuid($id);
        $this->service->update($model, $request->except(['user_uuid', 'is_default']));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyWebsitePage($id)
    {
        $this->myService->deleteMyWebsitePageByUuid($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUnpublishedWebsitePage(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => WebsitePage::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedWebsitePage($id)
    {
        $model = $this->service->showWebsitePageForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UnpublishedWebsitePageRequest $request
     * @return JsonResponse
     */
    public function storeUnpublishedWebsitePage(UnpublishedWebsitePageRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getKey()
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedWebsitePageRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editUnpublishedWebsitePage(UpdateUnpublishedWebsitePageRequest $request, $id)
    {
        $model = $this->service->showWebsitePageForEditorById($id);
        $this->service->update($model, $request->except(['user_uuid']));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishWebsitePageRequest $request
     * @return JsonResponse
     */
    public function changeStatusWebsitePage(AcceptPublishWebsitePageRequest $request)
    {
        $websitePageUuids = $request->website_pages;
        foreach ($websitePageUuids as $websitePageUuid)
        {
            $model = $this->service->findOneById($websitePageUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == WebsitePage::REJECT_PUBLISH_STATUS){
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
    public function getWebsitePagesDefault(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
        ]);
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showWebsitePagesDefault($id)
    {
        $model = $this->service->findOneWhereOrFail([
            ['publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
            ['uuid', $id]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

}
