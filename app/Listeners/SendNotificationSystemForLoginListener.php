<?php

namespace App\Listeners;

use App\Events\SendNotificationSystemForLoginEvent;
use App\Mail\SendNotificationSystem;
use App\Services\NotificationService;
use App\Services\SmtpAccountService;
use App\Services\UserTrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationSystemForLoginListener implements ShouldQueue
{
    private $service;

    private $smtpAccountService;

    private $notificationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserTrackingService $service,
        SmtpAccountService  $smtpAccountService,
        NotificationService $notificationService
    )
    {
        $this->service = $service;
        $this->smtpAccountService = $smtpAccountService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     *
     * @param SendNotificationSystemForLoginEvent $event
     * @return void
     */
    public function handle(SendNotificationSystemForLoginEvent $event)
    {
        $user = $event->user;
        $ip = $event->ip;
        $type = "account";

//        $ip = geoip()->getClientIP();
//        // 92.38.148.61, 171.248.187.0
        try {
            $geoIp = geoip()->getLocation($ip);

            $country = $geoIp->country;

            $userTracking = $this->service->findOneWhere([
                'user_uuid' => $user->uuid,
            ]);

            if ($userTracking) {
                if (($userTracking->last_login_location && $userTracking->last_login_location != $country) ||
                    (!$userTracking->last_login_location && $userTracking->register_location != $country) ){
                    $this->smtpAccountService->sendEmailNotificationSystem($user, new SendNotificationSystem($user, $type, $country));
                    $this->notificationService->create([
                        'type' => $type,
                        'type_uuid' => null,
                        'content' => ['langkey' => $type.'_login', 'country' => $country],
                        'user_uuid' => $user->uuid,
                    ]);
                }
                $this->service->update($userTracking, [
                    'ip' => $ip,
                    'last_login_location' => $country,
                    'postal_code' => $geoIp->postal_code
                ]);
            }else{
                $this->service->create([
                    'ip' => $ip,
                    'user_uuid' => $user->uuid,
                    'register_location' => $country,
                    'postal_code' => $geoIp->postal_code
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

}
}
