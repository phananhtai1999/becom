<?php

namespace App\Listeners;

use App\Events\SendEmailVerifyEmailEvent;
use App\Mail\SendVerifyEmailToUser;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;

class SendEmailVerifyEmailListener
{
    /**
     * @var UserService
     */
    private $userService;

    private $smtpAccountService;

    private $configService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserService $userService,
        SmtpAccountService $smtpAccountService,
        ConfigService $configService)
    {
        $this->userService = $userService;
        $this->smtpAccountService = $smtpAccountService;
        $this->configService = $configService;
    }

    /**
     * Handle the event.
     *
     * @param SendEmailVerifyEmailEvent $event
     * @return void
     */
    public function handle(SendEmailVerifyEmailEvent $event)
    {
        $user = $event->user;

        $smtpAccountConfig = $this->configService->findOneWhere([
            'key' => 'smtp_account'
        ]);

        $model = $this->userService->findOneWhere([[
            'email', $user->email
        ]]);

        $verifyEmail = $this->userService->update($model,[
            'email_verification_code' => rand(100000, 999999)
        ]);

        $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccountConfig->value);
        Mail::to($user->email)->send(new SendVerifyEmailToUser($model));
    }
}
