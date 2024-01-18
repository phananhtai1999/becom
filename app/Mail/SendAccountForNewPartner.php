<?php

namespace App\Mail;

use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAccountForNewPartner extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $password;

    /**
     * Create a new message instance.
     *
     * @param $email
     * @param $passwordReset
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
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
