<?php

namespace Leysco\GatePassManagementModule\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Leysco\GatePassManagementModule\Models\OGMS;
use Leysco\GatePassManagementModule\Reports\DoesNotExist;
use Leysco\GatePassManagementModule\Reports\ScanLogReport;
use Leysco\GatePassManagementModule\Reports\DocumentReport;
use Leysco\GatePassManagementModule\Mail\GPMDailyReportMail;
use Leysco\GatePassManagementModule\Reports\DublicateScanLogs;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OADM;

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
