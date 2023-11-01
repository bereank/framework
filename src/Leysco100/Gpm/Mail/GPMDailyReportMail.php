<?php

namespace Leysco100\Gpm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Leysco100\Shared\Models\MarketingDocuments\Models\GMS1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OGMS;

use function PHPUnit\Framework\isNull;

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
                $totalReleased = number_format(count(OGMS::where('Status', '!=', 0)

                        ->get()));


                $allDocumentYesterday = OGMS::select('id', 'Status', 'created_at')
                        // ->distinct('ExtRefDocNum')
                        ->whereDate('updated_at', $this->date);

                $totalSyncY = number_format(count($allDocumentYesterday->get()));
                $totalOpenY = number_format(OGMS::select('id', 'Status', 'updated_at')->whereDate('updated_at', $this->date)->where('Status', '!=', 3)
                        // ->distinct('ExtRefDocNum')
                        ->count());

                // $totalPendingRelY = number_format(OGMS::select('id', 'Status', 'updated_at')->whereDate('updated_at', $this->date)->where('Status', 1)

                //         // ->distinct('ExtRefDocNum')
                //         ->count());

                // $totalFlaggedY =  number_format(OGMS::select('id', 'Status', 'updated_at')->whereDate('updated_at', $this->date)->where('Status', 2)
                //         // ->distinct('ExtRefDocNum')
                //         ->count());

                $totalReleasedY = number_format(OGMS::select('id', 'Status', 'updated_at')
                        ->whereDate('updated_at', $this->date)->where('Status', 3)
                        ->where('ScanLogID', '!=', null)
                        // ->distinct('ExtRefDocNum')
                        ->count());
                $totalReleasedbyTargetY = number_format(OGMS::select('id', 'Status', 'updated_at')->whereDate('updated_at', $this->date)
                        // ->distinct('ExtRefDocNum')

                        ->where('Status', 3)
                        ->where('ScanLogID', null)
                        ->count());

                $totalCancelledY = number_format(OGMS::select('id', 'Status', 'updated_at')->whereDate('updated_at', $this->date)
                        // ->distinct('ExtRefDocNum')
                        ->where('Status', 4)
                        ->count());

                $totalYesterdayScans = GMS1::whereDate('created_at', $this->date)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->count();

                $totalFaildDoesNotExistY = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 1)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->count());

                $totalFailedDuplicateY = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 2)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->count());

                $totalSuccessfulReleased = GMS1::whereDate('created_at', $this->date)->where('Status', 0)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->where('Released', 1)->count();

                $totalSuccessfulNotReleased = GMS1::whereDate('created_at', $this->date)->where('Status', 0)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->where('Released', 0)->count();


                $totalCancelled = GMS1::whereDate('created_at', $this->date)->where('Status', 4)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->where('Released', 0)->count();

                $totalFlagged = number_format(GMS1::whereDate('created_at', $this->date)->where('Status', 3)
                        ->with('document', function ($query) {
                                $query->where('created_at', $this->date);
                        })
                        ->count());

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
                        'totalReleasedbyTargetY' => $totalReleasedbyTargetY,
                        'totalFaildDoesNotExistY' => $totalFaildDoesNotExistY,
                        'totalFailedDuplicateY' => $totalFailedDuplicateY,
                        'totalSuccessfulNotReleased' => $totalSuccessfulNotReleased,
                        'totalSuccessfulReleased' => $totalSuccessfulReleased,
                        'totalFlagged' => $totalFlagged,
                        'totalCancelledDocs' => $totalCancelledY,
                        'totalCancelledLogs' =>   $totalCancelled
                ];

                return $this->subject(Tenant::current()->name . ' :: ' . $this->date  . ' - ' . "  GPM Report")
                        ->markdown('gpm::ReportNotification')
                        ->with('date', $this->date)
                        ->attach($url)
                        ->attach($url_2)
                        ->attach($does_not_exist)
                        ->attach($DocumentReport)
                        ->with('summaryReport', $summaryReport);
        }
}
