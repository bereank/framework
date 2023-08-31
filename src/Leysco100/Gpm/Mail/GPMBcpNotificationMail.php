<?php

namespace Leysco100\Gpm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GPMBcpNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       $message = $this->message;

       $subject = "GPM Backup-Mode Notification ";

        return $this->subject($subject)
            ->markdown('gpm::BcpNotification')
            ->with('message', $message);
    }
}