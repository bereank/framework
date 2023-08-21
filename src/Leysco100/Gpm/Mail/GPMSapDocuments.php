<?php

namespace Leysco100\Gpm\Mail;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Leysco100\Shared\Models\MarketingDocuments\Models\OGMS;


class GPMSapDocuments extends Mailable
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
                $allDocument = OGMS::select('id', 'Status');


                //        $totalSync =  number_format(count($allDocument->get()));
                //        $totalOpen = number_format(count($allDocument->where('Status', 0)->get()));
                //        $totalReleased = number_format(count(OGMS::where('Status', '!=', 0)->get()));



                //        $date =  Carbon::now()->subDays(1)->format('Y-m-d');


                //        $allDocumentYesterday = OGMS::select('id', 'Status', 'GenerationDateTime')->whereDate('GenerationDateTime', $date);

                //        $totalSyncY = number_format(count($allDocumentYesterday->get()));
                //        $totalOpenY = number_format(count($allDocumentYesterday->where('Status', 0)->get()));
                //        $totalReleasedY = number_format(count(OGMS::select('id', 'Status', 'GenerationDateTime')->whereDate('GenerationDateTime', $date)->where('Status', '!=', 0)->get()));


                //        $totalYesterdayScans = GMS1::whereDate('created_at', Carbon::yesterday())->count();
                //        $totalFaildDoesNotExistY = number_format(GMS1::whereDate('created_at', Carbon::yesterday())->where('Status', 1)->count());
                //        $totalFailedDuplicateY = number_format(GMS1::whereDate('created_at', Carbon::yesterday())->where('Status', 2)->count());
                //        $totalSuccessful = GMS1::whereDate('created_at', Carbon::yesterday())->where('Status', 0)->count();
                //        $totalFlagged = number_format(GMS1::whereDate('created_at', Carbon::yesterday())->where('Status', 3)->count());
                $url =  Storage::path('ExportScanLogReport.xlsx');

                $summaryReport = [
                        //            'totalSync' => $totalSync,
                        //            'totalOpen' =>  $totalOpen,
                        //            'totalReleased' => $totalReleased,
                        //            'totalSyncY' => $totalSyncY,
                        //            'totalOpenY' =>  $totalOpenY,
                        //            'totalYesterdayScans' => $totalYesterdayScans,
                        //            'totalReleasedY' => $totalReleasedY,
                        //            'totalFaildDoesNotExistY' => $totalFaildDoesNotExistY,
                        //            'totalFailedDuplicateY' => $totalFailedDuplicateY,
                        //            'totalSuccessful' => $totalSuccessful,
                        //            'totalFlagged' => $totalFlagged
                ];
                //     $date = Carbon::now()->format('j-F-Y');
                return $this->subject(" GPM Scan Log Report")
                        ->markdown('gpm::SapDocuments')
                        //            ->with('date', $date)
                        ->attach($url)
                        ->with('summaryReport', $summaryReport);
        }
}
