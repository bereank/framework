<?php

namespace App\Domains\InventoryAndProduction\Jobs;

use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OITW;
use App\Models\THRDP;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateItemQuantities implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     * @var int $type 1=Inventory Quantities, 2=Const Center Quantities, 3=SAP Payments
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->type; //$type 1=Inventory Quantities, 2=Const Center Quantities, 3=SAP Payments
        $data = $this->data;

        /**
         *
         * Update Inventory Quantities
         */

        if ($this->type == 1) {
            foreach ($data as $key => $value) {
                $item = OITM::where('ItemCode', $value['productCode'])->first();
                if (!$item) {
                    continue;
                }

                $itemPrice = OITW::updateOrCreate([
                    'ItemCode' => $item->ItemCode,
                    'WhsCode' => $value['warehouseCode'],
                    'ItemID' => $item->id,
                ], [
                    'AvgPrice' => $value['AvgPrice'],
                    'OnHand' => $value['available_stock'],
                    'IsCommited' => $value['committed_stock'] ?? 0,
                ]);

                $totalItems = OITW::where('ItemCode', $item->ItemCode)
                    ->sum('OnHand');

                $item->update([
                    'OnHand' => $totalItems,
                ]);
            }
        }

        /**
         * Cost Center Quantities
         */

        if ($type == 2) {
        }

        /**
         * THIRD PARTY PAYMENTS
         */

        if ($type == 3) {
            foreach ($data as $key => $val) {
                $data = THRDP::updateOrCreate([
                    'TransID' => $val['TransID'],
                    'ExtRef' => $val['ExtRef'],
                ], [
                    'ActCode' => $val['ActCode'],
                    'CntName' => $val['CntName'],
                    'CntPhone' => $val['CntPhone'],
                    'TransAmount' => $val['TransAmount'],
                    'Balance' => $val['Balance'],
                ]);
            }
        }
    }
}
