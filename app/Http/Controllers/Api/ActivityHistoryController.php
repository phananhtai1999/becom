<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\ActivityHistoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ParseBodyMailTemplateRequest;
use App\Http\Requests\UpdateActivityHistoryRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\ActivityHistoryResource;
use App\Http\Resources\ActivityHistoryResourceCollection;
use App\Services\ActivityHistoryService;
use App\Services\MyActivityHistoryService;
use App\Services\MyContactService;

class ActivityHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @var
     */
    protected $myContactService;

    /**
     * @param ActivityHistoryService $service
     * @param MyActivityHistoryService $myService
     * @param MyContactService $myContactService
     */
    public function __construct(
        ActivityHistoryService   $service,
        MyActivityHistoryService $myService,
        MyContactService $myContactService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->myContactService = $myContactService;
        $this->resourceCollectionClass = ActivityHistoryResourceCollection::class;
        $this->resourceClass = ActivityHistoryResource::class;
        $this->storeRequest = ActivityHistoryRequest::class;
        $this->editRequest = UpdateActivityHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyActivityHistories(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            ));
    }

    /**
     * @param ParseBodyMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderBodyMailTemplate(ParseBodyMailTemplateRequest $request) {
        $model = $this->service->findOneById($request->activity_uuid);
        $renderBody = $this->service->renderBody($model->uuid, $model->type, 'html');

        return $this->sendOkJsonResponse(['data' =>
            ['body' => $renderBody]
        ]);
    }

    /**
     * @param ParseBodyMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderMyBodyMailTemplate(ParseBodyMailTemplateRequest $request) {
        $model = $this->service->findOneById($request->activity_uuid);
        $this->myContactService->findMyContactByKeyOrAbort($model->contact_uuid);

        $renderBody = $this->service->renderBody($model->uuid, $model->type, 'html');

        return $this->sendOkJsonResponse(['data' =>
            ['body' => $renderBody]
        ]);
    }
}
