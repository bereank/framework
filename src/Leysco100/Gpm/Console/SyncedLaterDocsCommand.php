<?php

namespace Leysco100\Gpm\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Leysco100\Gpm\Jobs\GPMDocsSyncronizationJob;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class SyncedLaterDocsCommand extends Command
{

    use TenantAware;
    protected $signature = 'gpm:check_if_synced {--tenant=*}';

    protected $description = 'Sync later docs';

    public function handle()
    {
        Log::info("Check sync scheduler");
        dispatch(new GPMDocsSyncronizationJob());
        $this->info('Synced later docs successfully.');
    }
}
