<?php

namespace App\Listeners;

use App\Events\ActivityHistoryEvent;
use App\Services\ActivityHistoryService;

class ActivityHistoryListener
{
    /**
     * @var ActivityHistoryService
     */
    public $activityHistoryService;

    /**
     * @param ActivityHistoryService $activityHistoryService
     */
    public function __construct(ActivityHistoryService $activityHistoryService)
    {
        $this->activityHistoryService = $activityHistoryService;
    }

    /**
     * @param ActivityHistoryEvent $event
     * @return void
     */
    public function handle(ActivityHistoryEvent $event)
    {
        $model = $event->model;
        $type = $event->type;
        $action = $event->action;

        if ($action === 'created') {
            $actionTranslate = 'đã tạo';
            $date = $model->created_at;
        } elseif ($action === 'updated') {
            $actionTranslate = 'đã sửa';
            $date = $model->updated_at;
        } elseif ($action === 'deleted') {
            $actionTranslate = 'đã xóa';
            $date = $model->deleted_at;
        }

        if ($type === 'note') {
            $typeTranslate = 'ghi chú';
            $this->activityHistoryService->create([
                'type' => $type,
                'type_id' => null,
                'content' => ['en' => "You $action $type at $date", 'vi' => "Bạn $actionTranslate $typeTranslate lúc $date"],
                'date' => $date,
                'contact_uuid' => $model->contact_uuid,
            ]);
        } elseif ($type === 'remind') {
            $typeTranslate = 'lời nhắc';
            foreach ($model->contacts as $contact) {
                $this->activityHistoryService->create([
                    'type' => $type,
                    'type_id' => null,
                    'content' => ['en' => "You $action $type at $date", 'vi' => "Bạn $actionTranslate $typeTranslate lúc $date"],
                    'date' => $date,
                    'contact_uuid' => $contact->uuid,
                ]);
            }
        }
    }
}
