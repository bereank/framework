<?php

namespace Leysco100\Gpm\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Leysco100\Shared\Models\Marketing\Models\GMS1;
use Leysco100\Shared\Models\Marketing\Models\OGMS;


class GPMExportLogsMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $from_date;
    public $to_date;
    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $allDocument = OGMS::select('id', 'Status');


        $totalSync =  number_format(count($allDocument->get()));
        $totalOpen = number_format(count($allDocument->where('Status', 0)->get()));
        $totalReleased = number_format(count(OGMS::where('Status', '!=', 0)->get()));


        $allDocumentYesterday = OGMS::select('id', 'Status', 'GenerationDateTime')->whereBetween('GenerationDateTime', [
            $this->from_date,
            $this->to_date,
        ]);

        $totalSyncY = number_format(count($allDocumentYesterday->get()));
        $totalOpenY = number_format(count($allDocumentYesterday->where('Status', 0)->get()));
        $totalReleasedY = number_format(count(OGMS::select('id', 'Status', 'GenerationDateTime')->whereBetween('GenerationDateTime', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', '!=', 0)->get()));


        $totalYesterdayScans = GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->count();
        $totalFaildDoesNotExistY = number_format(GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', 1)->count());
        $totalFailedDuplicateY = number_format(GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', 2)->count());

        $totalSuccessfulReleased = GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', 0)->where('Released', 1)->count();

        $totalSuccessfulNotReleased = GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', 0)->where('Released', 0)->count();

        $totalFlagged = number_format(GMS1::whereBetween('created_at', [
            $this->from_date,
            $this->to_date,
        ])->where('Status', 3)->count());
        $url =  Storage::path('ScanReport.xlsx');
        $url_2 =  Storage::path('DuplicateReport.xlsx');

        $does_not_exist = Storage::path('DoesNotExist.xlsx');
        $DocumentReport = Storage::path('DocumentReport.xlsx');

        $summaryReport = [
            'totalSync' => $totalSync,
            'totalOpen' =>  $totalOpen,
            'totalReleased' => $totalReleased,
            'totalSyncY' => $totalSyncY,
            'totalOpenY' =>  $totalOpenY,
            'totalYesterdayScans' => $totalYesterdayScans,
            'totalReleasedY' => $totalReleasedY,
            'totalFaildDoesNotExistY' => $totalFaildDoesNotExistY,
            'totalFailedDuplicateY' => $totalFailedDuplicateY,
            'totalSuccessfulNotReleased' => $totalSuccessfulNotReleased,
            'totalSuccessfulReleased' => $totalSuccessfulReleased,
            'totalFlagged' => $totalFlagged
        ];

        return $this->subject($this->from_date . ' To ' . $this->to_date  . ' - ' . "  GPM Report")
            ->markdown('gatepassmanagement::LongReportNotification')
            ->with('from_date', $this->from_date)
            ->with('to_date', $this->to_date)
            ->attach($url)
            ->attach($url_2)
            ->attach($does_not_exist)
            ->attach($DocumentReport)
            ->with('summaryReport', $summaryReport);
    }
}
