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

    public $replyToEmail;

    public $replyName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailTemplate, $mailFromName, $mailFromAddress, $replyToEmail, $replyName)
    {
        $this->mailTemplate = $mailTemplate;
        $this->mailFromName = $mailFromName;
        $this->mailFromAddress = $mailFromAddress;
        $this->replyToEmail = $replyToEmail;
        $this->replyName = $replyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mailFromAddress, $this->mailFromName)
            ->replyTo($this->replyToEmail, $this->replyName)
            ->subject($this->mailTemplate->subject)
            ->view('mail.SendCampaign');
    }
}
