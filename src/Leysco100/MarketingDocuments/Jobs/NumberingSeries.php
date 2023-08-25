<?php

namespace Leysco100\MarketingDocuments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\Administration\Models\NNM1;

class NumberingSeries implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $Series;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($Series)
    {
        $this->Series = $Series;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $NextNumber = NNM1::where('id', $this->Series)->value('NextNumber') + 1;
        $nnm1 = NNM1::where('id', $this->Series)->update(['NextNumber' => $NextNumber]);

        return "Updated";
    }
}
