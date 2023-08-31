<?php
namespace Leysco100\Gpm\Services;

use Illuminate\Support\Facades\Mail;
use Leysco100\Gpm\Mail\GPMBcpNotificationMail;

class NotificationsService
{
    public function sendNotification($recipient,$message)
    {
        Mail::to($recipient)->send(new GPMBcpNotificationMail($message));
    }
}