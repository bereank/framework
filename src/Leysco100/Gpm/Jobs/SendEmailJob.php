<?php

namespace Leysco100\Gpm\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Gpm\Mail\GPMNotificationMail;


class SendEmailJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $emails;
    protected $newRecordId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emails, $newRecordId)
    {
        $this->emails = $emails;
        $this->newRecordId = $newRecordId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // dd($this->newRecordId, $this->emails);
        Mail::to($this->emails)->send(new GPMNotificationMail($this->newRecordId));
    }
}
