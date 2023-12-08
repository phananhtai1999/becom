<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ReadNotificationsRequest;
use App\Http\Requests\UnReadNotificationsRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationResourceCollection;
use App\Services\MyNotificationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends AbstractRestAPIController
{
    use  RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param NotificationService $service
     * @param MyNotificationService $myService
     */
    public function __construct(
        NotificationService   $service,
        MyNotificationService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = NotificationResourceCollection::class;
        $this->resourceClass = NotificationResource::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        app($this->indexRequest);

        $models = $this->service->getCollectionWithPagination();
        $statistical = $this->service->statisticalNotification();

        $data = $this->service->resourceCollectionToData($this->resourceCollectionClass, $models);
        $data['meta']['total_notifications'] = $statistical->total;
        $data['meta']['total_read'] = $statistical->total_read;
        $data['meta']['total_not_read'] = $statistical->total_not_read;

        return $this->sendOkJsonResponse($data);
    }

    public function indexMy()
    {
        $request = app($this->indexRequest);

        $models = $this->service->getCollectionByUserIdAndAppIdWithPagination($request);
        $statistical = $this->service->statisticalNotification([
            "user_uuid" => auth()->user(),
            "app_id" => auth()->appId()
        ]);

        $data = $this->myService->resourceCollectionToData($this->resourceCollectionClass, $models);
        $data['meta']['total_notifications'] = $statistical->total;
        $data['meta']['total_read'] = $statistical->total_read;
        $data['meta']['total_not_read'] = $statistical->total_not_read;

        return $this->sendOkJsonResponse($data);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMy($id)
    {
        $model = $this->myService->findOneWhereOrFail([
            'uuid' => $id,
            'user_uuid' => auth()->user(),
            'app_id' => auth()->appId(),
        ]);

        $this->myService->destroy($model->getKey());

        return $this->sendOkJsonResponse();
    }

    public function readNotifications(ReadNotificationsRequest $request): JsonResponse
    {
        $this->service->updateReadByNotifications($request->get('notifications'), true);

        return $this->sendOkJsonResponse();
    }

    public function unreadNotifications(UnReadNotificationsRequest $request): JsonResponse
    {
        $this->service->updateReadByNotifications($request->get('notifications'), false);

        return $this->sendOkJsonResponse();
    }

    public function getNotificationCategories()
    {
        return $this->sendOkJsonResponse(['data' => config('notificationsystem.categories')]);
    }
}
