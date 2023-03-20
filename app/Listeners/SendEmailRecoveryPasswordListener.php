<?php

namespace App\Listeners;

use App\Events\SendEmailRecoveryPasswordEvent;
use App\Mail\SendRecoveryPasswordEmailToUser;
use App\Services\ConfigService;
use App\Services\SmtpAccountService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use App\Services\PasswordResetService;
use Illuminate\Support\Str;

class SendEmailRecoveryPasswordListener
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PasswordResetService
     */
    private $passwordResetService;

    private $smtpAccountService;

    private $configService;
    /**
     * Create the event listener.
     *
     * @param UserService $userService
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(
        UserService $userService,
        PasswordResetService $passwordResetService,
        SmtpAccountService $smtpAccountService,
        ConfigService $configService
    )
    {
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
        $this->smtpAccountService = $smtpAccountService;
        $this->configService = $configService;
    }

    /**
     * Handle the event.
     *
     * @param SendEmailRecoveryPasswordEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(SendEmailRecoveryPasswordEvent $event)
    {
        $user = $event->user;

        $smtpAccountConfig = $this->configService->findOneWhere([
            'key' => 'smtp_account'
        ]);

        $passwordReset = $this->passwordResetService->findOneWhere([[
            'email', $user->email
        ]]);

        if ($passwordReset) {
            $passwordReset->delete();
        }

        $passwordReset = $this->passwordResetService->create([
            'email' => $user->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccountConfig->value);
        Mail::to($user->email)->send(new SendRecoveryPasswordEmailToUser($user, $passwordReset));
    }
}
