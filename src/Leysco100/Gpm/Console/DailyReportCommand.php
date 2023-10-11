<?php

namespace Leysco100\Gpm\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Multitenancy\Models\Tenant;
use Leysco100\Gpm\Reports\DoesNotExist;
use Leysco100\Gpm\Reports\ScanLogReport;
use Leysco100\Gpm\Reports\DocumentReport;
use Leysco100\Gpm\Mail\GPMDailyReportMail;
use Leysco100\Gpm\Reports\DublicateScanLogs;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;

class DailyReportCommand extends Command
{
    use TenantAware;
    protected $signature = 'gpm:send-report {--tenant=*}';

    protected $description = 'Send Report';

    public function handle()
    {
        // $usersWithGates = DB::connection('tenant')->table('notify_users')
        //     ->select('UserSign', 'gate_id')
        //     ->groupBy('UserSign', 'gate_id')
        //     ->get();

        // $userGatesArray = [];

        // foreach ($usersWithGates as $userWithGate) {
        //     $userId = $userWithGate->UserSign;
        //     $gateId = $userWithGate->gate_id;

        //     if (!isset($userGatesArray[$userId])) {
        //         $userGatesArray[$userId] = [];
        //     }

        //     $userGatesArray[$userId][] = $gateId;
        // }
        // foreach($userGatesArray as $user => $gates){
        //  $user=   User::find($user);
        //  $emails = explode(';', $user->email);
        //     Log::info( $emails);
        //     Log::info($gates );



        $date =  Carbon::now()->subDays(1)->format('Y-m-d');

        Excel::store(new ScanLogReport($date), 'ScanReport.xlsx');
        Excel::store(new DublicateScanLogs($date), 'DuplicateReport.xlsx');
        Excel::store(new DoesNotExist($date), 'DoesNotExist.xlsx');
        Excel::store(new DocumentReport($date), 'DocumentReport.xlsx');
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);

        Mail::to($emails)->send(new GPMDailyReportMail($date));
    }
    // }
}
