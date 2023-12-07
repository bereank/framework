<?php

namespace Leysco100\Gpm\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Leysco100\Gpm\Mail\GPMBCPReportMail;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Gpm\Models\BackUpModeSetup;

class BCPNotificationJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {

        $this->id = $id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Send email after :" . $this->id . "Mins");
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);
        $updatedId = BackUpModeSetup::where('activatable_type', 1)->latest()
            ->where('Enabled', 1)->where('EndTime', '<', Carbon::now())->select('id')->first();
        Mail::to($emails)->send(new GPMBCPReportMail($updatedId));
    }
}