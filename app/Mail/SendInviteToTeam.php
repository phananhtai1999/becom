<?php

namespace App\Mail;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInviteToTeam extends Mailable
{
    use Queueable, SerializesModels;

    public Invite $invite;
    public $url;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invite $invite, $url, $password = null)
    {
        $this->invite = $invite;
        $this->url = $url;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.InviteEmail');
    }
}
