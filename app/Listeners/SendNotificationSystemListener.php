<?php

namespace App\Listeners;

use App\Events\SendNotificationSystemEvent;
use App\Mail\SendNotificationSystem;
use App\Services\NotificationService;
use App\Services\SmtpAccountService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationSystemListener implements ShouldQueue
{
    private $smtpAccountService;

    private $notificationService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        SmtpAccountService $smtpAccountService,
        NotificationService $notificationService
    )
    {
        $this->smtpAccountService = $smtpAccountService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param SendNotificationSystemEvent $event
     * @return void
     */
    public function handle(SendNotificationSystemEvent $event)
    {
        $user = $event->user;
        $type = $event->type;
        $action = $event->action;
        $model = $event->model;

        if (!$user){
            $user = $model->user;
        }


        try {
            $mail = new SendNotificationSystem($user, $type, $action, $model);
            $this->smtpAccountService->sendEmailNotificationSystem($user, $mail);
            $timezone = optional($this->smtpAccountService->getConfigByKeyInCache('timezone'))->value;
            if ($type === 'campaign') {
                $content = ['langkey' => $type.'_'.$action, 'type' => $type , 'name' => $model->tracking_key  , 'date' => Carbon::now($timezone)->toDateTimeString()];
            } else {
                $content = ['langkey' => $type.'_'.$action, 'type' => $type , 'name' => $model->name, 'date' => Carbon::now($timezone)->toDateTimeString()];
            }


            $this->notificationService->create([
                'type' => $type,
                'type_uuid' => $model->uuid,
                'content' => $content,
                'user_uuid' => $user->uuid,
                'app_id' => $user->app_id,
            ]);
        }catch (\Exception $e) {
            Log::error('Error Queue: ' . $e->getMessage());
        }
    }
}
