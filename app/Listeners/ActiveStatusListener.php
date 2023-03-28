<?php

namespace App\Listeners;

use App\Events\ActiveStatusEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;
use App\Services\StatusService;

class ActiveStatusListener
{
    /**
     * @var ActivityHistoryService
     */
    public $contactService;

    /**
     * @var StatusService
     */
    public $statusService;

    /**
     * @param ContactService $contactService
     * @param StatusService $statusService
     */
    public function __construct(
        ContactService $contactService,
        StatusService  $statusService
    )
    {
        $this->contactService = $contactService;
        $this->statusService = $statusService;
    }

    /**
     * @param ActiveStatusEvent $event
     * @return void
     */
    public function handle(ActiveStatusEvent $event)
    {
        $contactsOpenMail = $this->contactService->addPointContactOpenByCampaign($event->campaign, $event->email);

        foreach ($contactsOpenMail as $contactOpenMail) {
            //Add 1 point when open mail or phone
            $this->contactService->update($contactOpenMail, [
                'points' => $contactOpenMail->points + 1
            ]);
            //Update status contact
            $statusAdmin = $this->statusService->firstStatusByPoint($contactOpenMail->points);
            if ($contactOpenMail->status_uuid && $contactOpenMail->status && $contactOpenMail->status->user_uuid == null && $statusAdmin) {
                $this->contactService->update($contactOpenMail, [
                    'status_uuid' => $statusAdmin->uuid
                ]);
            }
        }
    }
}
