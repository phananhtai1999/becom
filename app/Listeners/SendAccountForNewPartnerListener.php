<?php

namespace App\Listeners;

use App\Events\SendAccountForNewPartnerEvent;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Mail\SendAccountForNewPartner;
use App\Mail\SendRecoveryPasswordEmailToUser;
use App\Services\ConfigService;
use App\Services\PasswordResetService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendAccountForNewPartnerListener implements ShouldQueue
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
    /**
     * Create the event listener.
     *
     * @param UserService $userService
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(
        UserService $userService,
        PasswordResetService $passwordResetService,
        SmtpAccountService $smtpAccountService
    )
    {
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
        $this->smtpAccountService = $smtpAccountService;
    }

    /**
     * Handle the event.
     *
     * @param SendAccountForNewPartnerEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(SendAccountForNewPartnerEvent $event)
    {
        $user = $event->user;

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

        $this->smtpAccountService->sendEmailNotificationSystem($user, new SendAccountForNewPartner($user, $passwordReset));
    }
}
