<?php
namespace Leysco100\Gpm\Services;

use Illuminate\Support\Facades\Mail;

class NotificationsService
{
    public function sendFailureNotification(\Exception $exception, $subject)
    {
        $recipient = 'robertkimaru1998@gmail.com';
        $body = 'An error occurred during the execution of Check if doc Synced Command: ' . $exception->getMessage();

        Mail::raw($body, function ($message) use ($recipient, $subject) {
            $message->to($recipient)->subject($subject);
        });
    }
}
