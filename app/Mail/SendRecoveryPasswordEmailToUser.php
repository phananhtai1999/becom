<?php

namespace App\Mail;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRecoveryPasswordEmailToUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var PasswordReset
     */
    public $passwordReset;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $passwordReset
     */
    public function __construct(
        User $user,
        PasswordReset $passwordReset
    ) {
        $this->user = $user;
        $this->passwordReset = $passwordReset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Reset Password'))
                    ->view('mail.recovery_password');
    }
}
