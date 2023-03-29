<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNotificationSystem extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $action;

    public $model;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $type, $action, $model = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->action = $action;
        $this->model = $model;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Notification System'))
            ->view('mail.SendNotificationSystem');
    }
}
