<?php

namespace Leysco100\Gpm\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GPMScanLogs extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date =  Carbon::now()->subDays(1)->format('Y-m-d');

        $url =  Storage::path('ExportScanReport.xlsx');

        return $this->subject($date  . ' - ' . "  GPM Scan Log Report")
            ->markdown('gpm::ScanLogNotification')
            ->with('date', $date)
            ->attach($url);
    }
}
