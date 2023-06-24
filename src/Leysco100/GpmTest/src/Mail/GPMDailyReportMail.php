<?php

namespace Leysco100\Gpm\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GMS1;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\OGMS;

class GPMDailyReportMail extends Mailable
{
        use Queueable;
        use SerializesModels;

        /**
         * Create a new message instance.
         *
         * @return void
         */
        public $date;
        public function __construct($date)
        {
                $this->date = $date;
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


                $allDocumentYesterday = OGMS::select('id', 'Status', 'GenerationDateTime')->whereDate('GenerationDateTime', $this->date);

                $totalSyncY = number_format(count($allDocumentYesterday->get()));
                $totalOpenY = number_format(count($allDocumentYesterday->where('Status', 0)->get()));
                $totalReleasedY = number_format(count(OGMS::select('id', 'Status', 'GenerationDateTime')->whereDate('GenerationDateTime', $this->date)->where('Status', '!=', 0)->get()));


                $totalYesterdayScans = GMS1::whereDate('created_at', $this->date)->count();
                $totalFaildDoesNotExistY = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 1)->count());
                $totalFailedDuplicateY = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 2)->count());

                $totalSuccessfulReleased = GMS1::whereDate('created_at', $this->date)->where('Status', 0)->where('Released', 1)->count();

                $totalSuccessfulNotReleased = GMS1::whereDate('created_at', $this->date)->where('Status', 0)->where('Released', 0)->count();

                $totalFlagged = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 3)->count());
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
                //     $this->date = Carbon::now()->format('j-F-Y');
                return $this->subject($this->date  . ' - ' . "  GPM Report")
                        ->markdown('gatepassmanagement::ReportNotification')
                        ->with('date', $this->date)
                        ->attach($url)
                        ->attach($url_2)
                        ->attach($does_not_exist)
                        ->attach($DocumentReport)
                        ->with('summaryReport', $summaryReport);
        }
}
