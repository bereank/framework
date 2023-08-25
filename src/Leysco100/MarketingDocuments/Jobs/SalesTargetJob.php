<?php

namespace Leysco100\MarketingDocuments\Jobs;


use App\Models\TargetItems;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SalesTargetJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $items;
    public $UoM;
    public $Headerid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($items, $UoM, $Headerid)
    {
        //
        $this->items = $items;
        $this->UoM = $UoM;
        $this->Headerid = $Headerid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        foreach ($this->items  as $item) {
            $Items = TargetItems::create([
                'UoM' => $UoM ?? "", //Target Metric
                'ItemCode' => $item['ItemCode'], //Target Val
                'target_setup_id' => $this->Headerid,
            ]);
        }
    }
}
