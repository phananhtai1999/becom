<?php

namespace App\Listeners;

use App\Events\SendEmailRecoveryPasswordEvent;
use App\Mail\SendRecoveryPasswordEmailToUser;
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

    /**
     * Create the event listener.
     *
     * @param UserService $userService
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(
        UserService $userService,
        PasswordResetService $passwordResetService
    )
    {
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
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

        Mail::to($user->email)->send(new SendRecoveryPasswordEmailToUser($user, $passwordReset));
    }
}
