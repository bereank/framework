<?php

namespace Leysco100\MarketingDocuments\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;

class PriceUpdateJob implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 360;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    protected $itemPricesArray;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $itemPricesArray)
    {
        $this->itemPricesArray = $itemPricesArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->itemPricesArray;

        if (!$data || count($data) <= 0) {
            return 0;
        }


        foreach ($data as $key => $value) {
            try {

                $priceUpdated = ITM1::updateOrCreate(
                    [
                        'ItemCode' => $value['ItemCode'],
                        'PriceList' => $value['PriceList'],
                    ],
                    [
                        'Price' => $value['Price'],
                        'Currency' => 1,
                    ]
                );

                Log::info(" PRICE UPDATED: . " . $priceUpdated->ItemCode);
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
                continue;
            }
        }
        return 0;
    }
}
