<?php

namespace App\Listeners;

use App\Events\ActivityHistoryOfSendByCampaignEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;

class ActivityHistoryOfSendByCampaignListener
{
    /**
     * @var ActivityHistoryService
     */
    public $activityHistoryService;

    /**
     * @var ContactService
     */
    public $contactService;

    /**
     * @param ActivityHistoryService $activityHistoryService
     * @param ContactService $contactService
     */
    public function __construct(
        ActivityHistoryService $activityHistoryService,
        ContactService         $contactService
    )
    {
        $this->activityHistoryService = $activityHistoryService;
        $this->contactService = $contactService;
    }

    /**
     * @param ActivityHistoryOfSendByCampaignEvent $event
     * @return void
     */
    public function handle(ActivityHistoryOfSendByCampaignEvent $event)
    {
        $mailSendingHistories = $event->model;
        $type = $event->type;

        if ($type === 'email') {
            $sendType = 'email';
        } else {
            $sendType = 'messages';
        }

        if ($mailSendingHistories->status === 'sent' || $mailSendingHistories->status === 'fail') {
            $contactUuid = $event->contact;
            $date = $mailSendingHistories->created_at;
            $content = ['langkey' => $mailSendingHistories->status === 'sent' ? 'sent.success' : 'sent.failed', 'send_type' => $sendType, 'email' => $mailSendingHistories->email, 'status' => $mailSendingHistories->status === 'sent' ? 'success' : 'failed', 'date' => $mailSendingHistories->created_at];
        } elseif ($mailSendingHistories->status === 'opened') {
            $contactUuid = $this->contactService->getContactByCampaignTypeEmail($mailSendingHistories->campaign->uuid, $mailSendingHistories->email)->toArray()[0]['uuid'];
            $date = $mailSendingHistories->updated_at;
            $content = ['langkey' => 'opened', 'email' => $mailSendingHistories->email, 'date' => $mailSendingHistories->updated_at];
        }

        $this->activityHistoryService->create([
            'type' => $type,
            'type_id' => $mailSendingHistories->uuid,
            'content' => $content,
            'date' => $date,
            'contact_uuid' => $contactUuid,
        ]);
    }
}