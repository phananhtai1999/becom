<?php

namespace App\Listeners;

use App\Events\ActivityHistoryOfSendByCampaignEvent;
use App\Services\ActivityHistoryService;

class ActivityHistoryOfSendByCampaignListener
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
     * @param ActivityHistoryOfSendByCampaignEvent $event
     * @return void
     */
    public function handle(ActivityHistoryOfSendByCampaignEvent $event)
    {
        $mailSendingHistories = $event->model;
        $type = $event->type;
        $contactUuid = $event->contact;

        if ($type === 'email') {
            $sendType = 'email';
        } else {
            $sendType = 'messages';
        }

        if ($mailSendingHistories->status === 'sent') {
            $status = 'success';
        } elseif ($mailSendingHistories->status === 'fail') {
            $status = 'failed';
        }

        $this->activityHistoryService->create([
            'type' => $type,
            'type_id' => $mailSendingHistories->uuid,
            'content' => ['langkey' => 'sent', 'send_type' => $sendType, 'email' => $mailSendingHistories->email, 'status' => $status, 'date' => $mailSendingHistories->created_at],
            'date' => $mailSendingHistories->created_at,
            'contact_uuid' => $contactUuid,
        ]);
    }
}
