<?php

namespace Leysco100\Gpm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Leysco100\Shared\Models\Marketing\Models\GMS1;
use Leysco100\Shared\Models\Marketing\Models\GPMGate;
use Leysco100\Shared\Models\Administration\Models\OADM;


class GPMNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $scanLogID;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($scanLogID)
    {
        $this->scanLogID = $scanLogID;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        $data = OADM::where('id', 1)->first();
        $documentData = GMS1::with('objecttype')->where('id', $this->scanLogID)->first();
        $gpmGate = GPMGate::where('id', $documentData->GateID)->first();

        $error = "Scann Successfully";
        if (!$documentData) {
            return;
        }
        if ($documentData->Status == 1) {
            $error = "The document does not exist in our database";
        }
        if ($documentData->Status == 2) {
            $error = "Duplicate Scan";
        }
        $gpmGateName =   $gpmGate->Name ?? "N/A";

        $subject = "GPM SCAN LOG : GATE  " .  $gpmGateName;


        return $this->subject($subject)
            ->markdown('gpm::ErrorNotification')
            ->with('documentData', $documentData)
            ->with('error', $error);
    }
}
