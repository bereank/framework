<?php

namespace Leysco100\Gpm\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Maatwebsite\Excel\Facades\Excel;

use Leysco100\Shared\Models\Administration\Models\OADM;


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
