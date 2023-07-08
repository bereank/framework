<?php

namespace Leysco100\Gpm\Console;

use Illuminate\Console\Command;
use Leysco100\Gpm\Jobs\GPMDocsSyncronizationJob;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class SyncedLaterDocsCommand extends Command
{

    use TenantAware;
    protected $signature = 'gpm:check_if_synced {--tenant=*}';

    protected $description = 'Sync later docs';

    public function handle()
    {
        dispatch(new GPMDocsSyncronizationJob());
        $this->info('Synced later docs successfully.');
    }
}
