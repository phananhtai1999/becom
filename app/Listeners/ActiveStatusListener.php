<?php

namespace App\Listeners;

use App\Events\ActiveStatusEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;
use App\Services\MyStatusService;
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
     * @var MyStatusService
     */
    public $myStatusService;

    /**
     * @param ContactService $contactService
     * @param StatusService $statusService
     * @param MyStatusService $myStatusService
     */
    public function __construct(
        ContactService  $contactService,
        StatusService   $statusService,
        MyStatusService $myStatusService
    )
    {
        $this->contactService = $contactService;
        $this->statusService = $statusService;
        $this->myStatusService = $myStatusService;
    }

    public function handle(ActiveStatusEvent $event)
    {
        $contactsOpenMail = $this->contactService->addPointContactOpenByCampaign($event->campaign, $event->email);
        //Get All status Admin
        $statusAdmin = $this->statusService->getAllStatusDefault();
        //Check status User exists or not
        $userUuid = $contactsOpenMail->unique('user_uuid')->pluck('user_uuid')->toArray();
        $statusUser = $this->myStatusService->getMyStatus($userUuid);

        foreach ($contactsOpenMail as $contactOpenMail) {
            //Add 1 point when open mail
            $this->contactService->update($contactOpenMail, [
                'points' => $contactOpenMail->points + 1
            ]);

            //Update status contact
            if ($statusUser->count() != 0) {
                $statusUserActive = $statusUser->where('points', '<=', $contactOpenMail->points)->sortByDesc('points')->first() ?: $this->statusService->firstStatusByUserUuid($contactOpenMail->user_uuid);
                $this->contactService->update($contactOpenMail, [
                    'status_uuid' => optional($statusUserActive)->uuid
                ]);
            } else {
                $statusAdminActive = $statusAdmin->where('points', '<=', $contactOpenMail->points)->sortByDesc('points')->first() ?: $this->statusService->firstStatusAdmin();
                $this->contactService->update($contactOpenMail, [
                    'status_uuid' => optional($statusAdminActive)->uuid
                ]);
            }
        }
    }
}
