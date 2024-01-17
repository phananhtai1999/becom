<?php

namespace App\Listeners;

use App\Services\PartnerUserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Techup\ApiBase\Events\AfterUserRegister;

class InvitePartnerAfterUserRegisterListener implements ShouldQueue
{
    /**
     * @var PartnerUserService
     */
    protected $partnerUserService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PartnerUserService $partnerUserService)
    {
        //
        $this->partnerUserService = $partnerUserService;
    }

    /**
     * Handle the event.
     *
     * @param  AfterUserRegister  $event
     * @return void
     */
    public function handle(AfterUserRegister $event)
    {
        $userProfile = $event->model;
        $cookies = $event->cookies;

        if (array_key_exists("invitePartner", $cookies)){
            $this->partnerUserService->create([
                'user_uuid' => $userProfile->user_uuid,
                'app_id' => $userProfile->app_id,
                'registered_from_partner_code' => $cookies['invitePartner']
            ]);
        }
    }
}
