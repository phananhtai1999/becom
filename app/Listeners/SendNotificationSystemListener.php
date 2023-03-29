<?php

namespace App\Listeners;

use App\Events\SendNotificationSystemEvent;
use App\Mail\SendNotificationSystem;
use App\Services\NotificationService;
use App\Services\SmtpAccountService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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

        $mail = new SendNotificationSystem($user, $type, $action, $model);
        $this->smtpAccountService->sendEmailNotificationSystem($user, $mail);
        if ($type === 'campaign') {
            $content = ['langkey' => $type.'_'.$action, 'type' => $type , 'name' => $model->tracking_key  , 'date' => Carbon::now()->toDateTimeString()];
        } else {
            $content = ['langkey' => $type.'_'.$action, 'type' => $type , 'name' => $model->name, 'date' => Carbon::now()->toDateTimeString()];
        }

        $this->notificationService->create([
            'type' => $type,
            'type_uuid' => $model->uuid,
            'content' => $content,
            'user_uuid' => $user->uuid,
        ]);
    }
}
