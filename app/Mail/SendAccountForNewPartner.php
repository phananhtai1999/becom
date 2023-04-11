<?php

namespace App\Mail;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAccountForNewPartner extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $passwordReset;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $passwordReset
     */
    public function __construct(User $user, PasswordReset $passwordReset)
    {
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
        return $this->subject('Welcome Partner')
            ->view('mail.SendAccountForNewPartner');
    }
}
