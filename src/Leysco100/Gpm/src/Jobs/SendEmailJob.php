<?php

namespace Leysco\Gpm\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Leysco\Gpm\Mail\GPMNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;


class SendEmailJob implements ShouldQueue
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
