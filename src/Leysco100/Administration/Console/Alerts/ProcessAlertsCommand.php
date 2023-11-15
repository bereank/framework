<?php

namespace Leysco100\Administration\Console\Alerts;

use Illuminate\Console\Command;
use Leysco100\Administration\Jobs\ProcessAlertsJob;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\Administration\Models\OALT;

class ProcessAlertsCommand extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:administration:process-alerts-command {--tenant=*}';

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

        $alert_temp = OALT::with(
            'alt1',
            'alt2',
            'alt3',
            'alt4.saved_query',
            'alt5',
            'alt6.saved_query'
        )->get();

        foreach ($alert_temp as $temp) {
            dispatch(new ProcessAlertsJob($temp));
        }
    }


}
