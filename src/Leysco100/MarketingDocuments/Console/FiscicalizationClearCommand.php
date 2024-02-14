<?php

namespace Leysco100\MarketingDocuments\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\MarketingDocuments\Models\FSC2;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;


class FiscicalizationClearCommand extends Command
{

    use TenantAware;
    protected $signature = 'marketing:clear_fiscal_docs {--tenant=*}';

    protected $description = 'Clear fiscal documents after every 2 mins';

    public function handle()
    {
        Log::info("Clearing fiscal documents");
        $time_five_minutes_ago = now()->subMinutes(5);

        FSC2::chunk(100, function ($records) use ($time_five_minutes_ago) {
            foreach ($records as $record) {
                $diff = $record->created_at->diffInMinutes($time_five_minutes_ago);
                if ($diff >  5) {
                    FSC2::where('id', $record->id)->delete();
                }
            }
        });

        $this->info('Cleared successfully.');
    }
}
