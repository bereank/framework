<?php

namespace Leysco100\Gpm\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Leysco100\Gpm\Mail\GPMScanLogs;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Leysco100\Gpm\Reports\ExportScanLog;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\Administration\Models\OADM;

class ExportScanLogsJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fromDate;
    public $toDate;
    public $fields;
    public $docNum;
    public $users;
    public $gates;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fromDate, $toDate, $fields, $docNum = null, $users, $gates)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->fields = [
            'Date',
            'Gate',
            'User',
            'Phone Number',
            'Document Type',
            'Document Number',
        ];
        $this->docNum = $docNum;
        $this->users = $users;
        $this->gates = $gates;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new ExportScanLog(
            fromDate: $this->fromDate,
            toDate: $this->toDate,
            fields: $this->fields,
            docNum: $this->docNum,
            users: $this->users,
            gates: $this->gates,
        ), 'ExportScanReport.xlsx');
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);

        Mail::to($emails)->send(new GPMScanLogs());
    }
}
