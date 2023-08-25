<?php

namespace Leysco100\MarketingDocuments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;

class InventoryUpdateJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    protected $itemPricesArray;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($itemPricesArray)
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

        try {
            $data = $this->itemPricesArray;
            foreach ($data as $key => $value) {
                $item = OITM::where('ItemCode', $value['ItemCode'])->first();
                if (!$item) {
                    continue;
                }

                $itemPrice = OITW::updateOrCreate([
                    'ItemCode' => $item->ItemCode,
                    'WhsCode' => $value['WhsCode'],
                ], [
                    'AvgPrice' => $value['AvgPrice'],
                    'OnHand' => $value['OnHand'],
                    'IsCommited' => $value['IsCommited'] ?? 0,
                ]);
                info("ITEM UPDATED:" . $item->ItemCode . " QUANTITIES POSTED: " . $value['OnHand']);
                $totalItems = OITW::where('ItemCode', $item->ItemCode)
                    ->sum('OnHand');

                $item->update([
                    'OnHand' => $totalItems,
                ]);
            }
        } catch (\Throwable $th) {
            Log::info($th);
        }
    }
}
