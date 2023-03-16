<?php

namespace App\Listeners;

use App\Events\ActivityHistoryEvent;
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
            $sendTypeTranslate = 'thư';
        } else {
            $sendType = 'messages';
            $sendTypeTranslate = 'tin nhắn';
        }

        if ($mailSendingHistories->status === 'sent') {
            $status = 'success';
            $statusTranslate = 'thành công';
            $messages = "You sent $sendType to $mailSendingHistories->email $status at $mailSendingHistories->created_at";
            $messagesTranslate = "Bạn đã gửi $sendTypeTranslate đến $mailSendingHistories->email $statusTranslate lúc $mailSendingHistories->created_at";
        } elseif ($mailSendingHistories->status === 'fail') {
            $status = 'failed';
            $statusTranslate = 'thất bại';
            $messages = "You sent $sendType to $mailSendingHistories->email $status at $mailSendingHistories->created_at";
            $messagesTranslate = "Bạn đã gửi $sendTypeTranslate đến $mailSendingHistories->email $statusTranslate lúc $mailSendingHistories->created_at";
        }

        $this->activityHistoryService->create([
            'type' => $type,
            'type_id' => $mailSendingHistories->uuid,
            'content' => ['en' => $messages, 'vi' => $messagesTranslate],
            'date' => $mailSendingHistories->created_at,
            'contact_uuid' => $contactUuid,
        ]);
    }
}
