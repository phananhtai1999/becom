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
use Techup\ApiBase\Events\AfterUserLogin;

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
     * @param AfterUserLogin $event
     * @return void
     */
    public function handle(AfterUserLogin $event)
    {
        $model = $event->model;
        $data = $event->data;
        $type = "account";

        try {
            if(isset($data['ip'])){
                $ip = $data['ip'];
                $geoIp = geoip()->getLocation($ip);
                $country = $geoIp->country;

                $userTracking = $this->service->findAllWhere([
                    'user_uuid' => $model->uuid,
                ]);
                if (!$userTracking->isEmpty()) {
                    $lastUserTracking = $userTracking->last();
                    if ($lastUserTracking->location != $country){
//                        $this->smtpAccountService->sendEmailNotificationSystem($model, new SendNotificationSystem($model, $type, $country));
                        $this->notificationService->create([
                            'type' => $type,
                            'type_uuid' => null,
                            'content' => ['langkey' => $type.'_login', 'country' => $country],
                            'user_uuid' => $model->uuid,
                            'app_id' => $model->app_id
                        ]);
                    }
                }

                $this->service->create([
                    'ip' => $ip,
                    'user_uuid' => $model->uuid,
                    'location' => $country,
                    'postal_code' => $geoIp->postal_code,
                    'user_agent' => isset($data['user_agent']) ? $data['user_agent']: null,
                    'app_id' => $model->app_id
                ]);

            }else{
                Log::debug("Not found ip");
            }
        }catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
