<?php

namespace Leysco100\Administration\Console\Alerts;

use Illuminate\Console\Command;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\Administration\Models\OALT;
use Leysco100\Administration\Services\AlertsManagerService;

class ResetAlertsCommand extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:administration:reset-alerts-command {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $alert_temp = OALT::get();

        foreach ($alert_temp as $temp) {
            $data = (new AlertsManagerService())->processPeriod(
                $temp->FrqncyType,
                $temp->FrqncyIntr,
                $temp->ExecTime,
                $temp->ExecDaY
            );
         
            OALT::where('id', $temp->id)->update([
                'ExecDaY' =>  $data['ExecDay'],
                'ExecTime' =>  $data['ExecTime'],
                'NextDate' =>  $data['NextDate'],
                'NextTime' =>  $data['NextTime'],
            ]);
        }
    }


}
