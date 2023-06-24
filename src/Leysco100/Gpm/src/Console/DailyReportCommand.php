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

class DailyReportCommand extends Command
{
    protected $signature = 'gpm:send-report';

    protected $description = 'Send Report';

    public function handle()
    {
        $date =  Carbon::now()->subDays(1)->format('Y-m-d');

        Excel::store(new ScanLogReport($date), 'ScanReport.xlsx');
        Excel::store(new DublicateScanLogs($date), 'DuplicateReport.xlsx');
        Excel::store(new DoesNotExist($date), 'DoesNotExist.xlsx');
        Excel::store(new DocumentReport($date), 'DocumentReport.xlsx');
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);

        Mail::to($emails)->send(new GPMDailyReportMail($date));
    }
}
