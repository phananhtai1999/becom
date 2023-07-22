<?php

namespace App\Listeners;

use App\Events\ActivityHistoryOfSendByCampaignEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;
use Carbon\Carbon;

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

        $content = null;
        $timezone = optional($this->activityHistoryService->getConfigByKeyInCache('timezone'))->value;
        if ($mailSendingHistories->status === 'sent' || $mailSendingHistories->status === 'fail') {
            $contactUuid = $event->contact;
            $date = $timezone ? Carbon::parse($mailSendingHistories->created_at)->setTimezone($timezone)->toDateTimeString() : $mailSendingHistories->created_at;
            $content = ['status_type' => $mailSendingHistories->status, 'langkey' => $mailSendingHistories->status === 'sent' ? 'sent.success' : 'sent.failed', 'send_type' => $sendType, 'email' => $mailSendingHistories->email, 'status' => $mailSendingHistories->status === 'sent' ? 'success' : 'failed', 'date' => $date];
        } elseif ($mailSendingHistories->status === 'opened') {
            $contactUuid = $this->contactService->getContactByCampaignTypeEmail($mailSendingHistories->campaign->uuid, $mailSendingHistories->email)->toArray()[0]['uuid'];
            $date =  $timezone ? Carbon::parse($mailSendingHistories->updated_at)->setTimezone($timezone)->toDateTimeString() : $mailSendingHistories->updated_at;
            $content = ['status_type' => $mailSendingHistories->status, 'langkey' => 'opened', 'email' => $mailSendingHistories->email, 'date' => $date];
        }
        if($content){
            $this->activityHistoryService->create([
                'type' => $type,
                'type_id' => $mailSendingHistories->uuid,
                'content' => $content,
                'date' => $date,
                'contact_uuid' => $contactUuid,
            ]);
        }
    }
}
