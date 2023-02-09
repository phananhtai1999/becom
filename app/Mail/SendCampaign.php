<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCampaign extends Mailable
{
    use Queueable, SerializesModels;

    public $mailTemplate;

    public $mailFromName;

    public $mailFromAddress;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailTemplate, $mailFromName, $mailFromAddress)
    {
        $this->mailTemplate = $mailTemplate;
        $this->mailFromName = $mailFromName;
        $this->mailFromAddress = $mailFromAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mailFromAddress, $this->mailFromName)->subject($this->mailTemplate->subject)
            ->view('mail.SendCampaign');
    }
}
