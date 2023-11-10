<?php

namespace Leysco100\Administration\Jobs;


use Illuminate\Bus\Queueable;
use Leysco100\Gpm\Mail\AlertMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\Administration\Models\OALR;
use Leysco100\Shared\Models\Administration\Models\Role;

class SendAlertEmailJob implements ShouldQueue, TenantAware
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected $alertId;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($alertId)
  {
    $this->alertId = $alertId;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $alert =   OALR::with('alert_template.alt1.users', 'alert_template.alt2.group')->where('id', $this->alertId)->first();

    $mails = [];

    foreach ($alert->alert_template->alt2 as $alt2) {
        $role = Role::find($alt2->group->id);
        $usersWithRole = $role->users;
        foreach ($usersWithRole as $userWithRole) {
            $mails[] = $userWithRole->email;
        }
    }

    foreach ($alert->alert_template->alt1 as $alt) {
        $mails[] = $alt->users->email;
    }

    $uniqueMails = array_values(array_unique($mails));
    Log::info("_________________SENDING MAIL__________________");
    Log::info(['mails' => $uniqueMails]);
    if (count($uniqueMails) > 0) {
        Mail::to($uniqueMails)->send(new AlertMail($alert->id));
    } else {
        Log::info("_________________MAIL Not found__________________");
    }
  }
}
