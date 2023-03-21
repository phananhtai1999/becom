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
            $date = $model->created_at;
        } elseif ($action === 'updated') {
            $date = $model->updated_at;
        } elseif ($action === 'deleted') {
            $date = $model->deleted_at;
        }

        if ($type === 'note') {
            $this->activityHistories($type, $model->uuid, $action, $model->contact->email, $date, $model->contact_uuid);
        } elseif ($type === 'remind') {
            foreach ($model->contacts as $contact) {
                $this->activityHistories($type, $model->uuid, $action, $contact->email, $date, $contact->uuid);
            }
        }
    }

    /**
     * @param $type
     * @param $typeId
     * @param $action
     * @param $contact
     * @param $date
     * @param $contactUuid
     * @return mixed
     */
    public function activityHistories($type, $typeId, $action, $contact, $date, $contactUuid)
    {
        return $this->activityHistoryService->create([
            'type' => $type,
            'type_id' => $typeId,
            'content' => ['langkey' => $action . '.' . $type, 'type' => $type, 'contact' => $contact, 'date' => $date],
            'date' => $date,
            'contact_uuid' => $contactUuid,
        ]);
    }
}
