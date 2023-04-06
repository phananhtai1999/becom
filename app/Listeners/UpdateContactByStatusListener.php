<?php

namespace App\Listeners;

use App\Events\UpdateContactByStatusEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;
use App\Services\StatusService;

class UpdateContactByStatusListener
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
        ContactService  $contactService,
        StatusService   $statusService
    )
    {
        $this->contactService = $contactService;
        $this->statusService = $statusService;
    }

    /**
     * @param UpdateContactByStatusEvent $event
     * @return void
     */
    public function handle(UpdateContactByStatusEvent $event)
    {
        $contacts = $this->contactService->findAllWhere([]);
        $statusAdmin = $this->statusService->getAllStatusDefault();
        $contacts->each(function ($contact) use ($statusAdmin) {
            $statusUser = $this->statusService->getAllStatusByUserUuid($contact->user_uuid);
            if ($statusUser->count() != 0) {
                $status = $statusUser->where('points', '<=', $contact->points)->sortByDesc('points')->first() ?: $this->statusService->firstStatusByUserUuid($contact->user_uuid);
                $this->contactService->update($contact, [
                    'status_uuid' => optional($status)->uuid
                ]);
            } else {
                $status = $statusAdmin->where('points', '<=', $contact->points)->sortByDesc('points')->first() ?: $this->statusService->firstStatusAdmin();
                $this->contactService->update($contact, [
                    'status_uuid' => optional($status)->uuid
                ]);
            }
        });
    }
}
