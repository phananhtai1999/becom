<?php

namespace App\Mail;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendActiveCode extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Otp $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Otp $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.SendActiveCode');
    }
}
