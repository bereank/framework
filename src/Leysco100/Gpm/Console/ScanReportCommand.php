<?php

namespace Leysco100\Gpm\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Leysco100\Gpm\Mail\GPMExportLogsMail;
use Leysco100\Gpm\Reports\LongScanLogReport;
use Leysco100\Gpm\Reports\LongReports\LongDoesNotExist;
use Leysco100\Gpm\Reports\LongReports\LongDocumentReport;
use Leysco100\Gpm\Reports\LongReports\LongDublicateScanLogs;



class ScanReportCommand extends Command
{
    protected $signature = 'gpm:send-leysco-gpm-report {emails} {from_date} {to_date}';

    protected $description = 'Send Report';

    public function handle()
    {



        $from_date = Carbon::parse($this->argument('from_date'))->startOfDay();
        $to_date = Carbon::parse($this->argument('to_date'))->endOfDay();


        Excel::store(new LongScanLogReport($from_date, $to_date), 'ScanReport.xlsx');
        Excel::store(new LongDocumentReport($from_date, $to_date), 'DocumentReport.xlsx');
        Excel::store(new LongDublicateScanLogs($from_date, $to_date), 'DuplicateReport.xlsx');
        Excel::store(new LongDoesNotExist($from_date, $to_date), 'DoesNotExist.xlsx');

        $emails = explode(',', $this->argument('emails'));

        Mail::to($emails)->send(new GPMExportLogsMail($from_date, $to_date));
    }
}
