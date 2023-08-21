<?php

namespace Leysco100\Gpm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Leysco100\Gpm\Reports\BCPScanReport;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeLines;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;

class GPMBCPReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $DocEntry;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($DocEntry)
    {
        $this->DocEntry = $DocEntry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setup = BackUpModeSetup::latest()->first();

        $error = "";

        $totalSynced = number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->where('SyncStatus', 1)->get()));
        $totalNotSynced = number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->where('SyncStatus', 0)->get()));
        $totalNotReleased = number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->where('ReleaseStatus', 0)->get()));
        $totalReleased = number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->where('ReleaseStatus', 1)->get()));
        $totalFlagged = number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->where('ReleaseStatus', 2)->get()));
        $totalDocs =     number_format(count(BackUpModeLines::where('DocEntry',  $setup->id)->get()));

        Excel::store(new BCPScanReport(), 'BCPScanReport.xlsx');
        $url_2 =  Storage::path('BCPScanReport.xlsx');
        $summaryReport = [
            'totalSynced' => $totalSynced,
            'totalDocs' =>  $totalDocs,
            'totalReleased' => $totalReleased,
            'totalNotReleased' => $totalNotReleased,
            'totalNotSynced' =>  $totalNotSynced,
            'totalFlagged' => $totalFlagged
        ];

        $subject = "GPM BCP REPORT ";


        return $this->subject($subject)
            ->markdown('gatepassmanagement::BCMNotification')
            ->with('summaryReport', $summaryReport)
            ->attach($url_2)
            ->with('error', $error);
    }
}
