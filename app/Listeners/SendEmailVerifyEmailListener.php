<?php

namespace App\Listeners;

use App\Events\SendEmailVerifyEmailEvent;
use App\Mail\SendVerifyEmailToUser;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;

class SendEmailVerifyEmailListener
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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

        $model = $this->userService->findOneWhere([[
            'email', $user->email
        ]]);

        $verifyEmail = $this->userService->update($model, array_merge(json_decode($user->all()), [
            'email_verification_code' => rand(100000, 999999)
        ]));

        Mail::to($user->email)->send(new SendVerifyEmailToUser($user, $verifyEmail));

    }
}
