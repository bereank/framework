<?php

namespace Leysco\Gpm\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Leysco\Gpm\Reports\DoesNotExist;
use Maatwebsite\Excel\Facades\Excel;
use Leysco\Gpm\Reports\ScanLogReport;
use Leysco\Gpm\Reports\DocumentReport;
use Leysco\Gpm\Mail\GPMDailyReportMail;
use Leysco\Gpm\Reports\DublicateScanLogs;


class LeyscoDailyReportsCommand extends Command
{
    protected $signature = 'gpm:send-leysco-report {emails} {date}';

    protected $description = 'Send Report';

    public function handle()
    {

        $date = $this->argument('date');


        Excel::store(new ScanLogReport($date), 'ScanReport.xlsx');
        Excel::store(new DocumentReport($date), 'DocumentReport.xlsx');
        Excel::store(new DublicateScanLogs($date), 'DuplicateReport.xlsx');
        Excel::store(new DoesNotExist($date), 'DoesNotExist.xlsx');

        $emails = explode(',', $this->argument('emails'));

        Mail::to($emails)->send(new GPMDailyReportMail($date));
    }
}
