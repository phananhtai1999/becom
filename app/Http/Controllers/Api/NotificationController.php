<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Requests\ReadNotificationsRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationResourceCollection;
use App\Services\MyNotificationService;
use App\Services\NotificationService;

class NotificationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestIndexMyTrait;
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

    public function readNotifications(ReadNotificationsRequest $request)
    {
        $notifications = $request->get('notifications');
        foreach ($notifications as $notificationUuid) {
            $notification = $this->service->findOneById($notificationUuid);
            $this->service->update($notification, [
                'read' => true
            ]);
        }

        return $this->sendOkJsonResponse();
    }
}
