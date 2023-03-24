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
        $contactsOpenMail = $this->contactService->getContactOpenByCampaign($event->campaign, $event->email);
        $activeStatus = $this->statusService->firstStatus();
        $silverStatus = $activeStatus['silver'];
        $goldStatus = $activeStatus['gold'];
        $platinumStatus = $activeStatus['platinum'];
        $diamondStatus = $activeStatus['diamond'];

        if ($silverStatus && $goldStatus && $platinumStatus && $diamondStatus) {
            foreach ($contactsOpenMail as $contactOpenMail) {
                $points = $contactOpenMail->points;
                if ($silverStatus->points <= $points && $points <= $goldStatus->points) {
                    $this->updateContact($contactOpenMail, $silverStatus->uuid);

                } elseif ($goldStatus->points < $points && $points <= $platinumStatus->points) {
                    $this->updateContact($contactOpenMail, $goldStatus->uuid);

                } elseif ($platinumStatus->points < $points && $points <= $diamondStatus->points) {
                    $this->updateContact($contactOpenMail, $platinumStatus->uuid);

                } elseif ($diamondStatus->points < $points) {
                    $this->updateContact($contactOpenMail, $diamondStatus->uuid);
                }
            }
        }
    }

    /**
     * @param $contactOpenMail
     * @param $statusUuid
     * @return void
     */
    public function updateContact($contactOpenMail, $statusUuid)
    {
        $this->contactService->update($contactOpenMail, [
            'status_uuid' => $statusUuid
        ]);
    }
}
