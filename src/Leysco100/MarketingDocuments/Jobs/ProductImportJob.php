<?php

namespace Leysco100\MarketingDocuments\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\Shared\Models\Administration\Models\OUGP;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITB;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;

class ProductImportJob implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $val = $this->data;
        $ItmsGrpCod = OITB::where('ExtRef', $val[3])->value('id');
        $UgpEntry = OUGP::where('ExtRef', $val[4])->value('id');
        $PriceUnit = OUOM::where('ExtRef', $val[5])->value('id');

        $salesUom = OUOM::where('UomName', $val[6])->first();
        $purchaseUom = OUOM::where('UomName', $val[8])->first();

        $invUom = OUOM::where('UomName', $val[10])->first();

        $newItem = OITM::updateOrcreate([
            'ItemCode' => $val[0],
            'ItemName' => $val[1],
        ], [
            'ItmsGrpCod' => $ItmsGrpCod,
            'UgpEntry' => $UgpEntry,
            'PriceUnit' => $PriceUnit,
            // // 'InvntItem' => $request['InvntItem'],
            // // 'SellItem' => $request['SellItem'],
            // // 'PrchseItem' => $request['PrchseItem'],
            // //General
            // 'ManBtchNum' => $request['ManBtchNum'], //Manage Batch No. [Yes/No]
            // 'ManSerNum' => $request['ManSerNum'], //Serial No. Management . [Yes/No]
            // //purchse
            'BuyUnitMsr' => $purchaseUom ? $purchaseUom->UomName : null, //Puchasing UoM Name
            'PUoMEntry' => $purchaseUom ? $purchaseUom->id : null, //Default Purchase UoM
            'NumInBuy' => $val[9],
            // 'PurPackUn' => $request['PurPackUn'], //Quantity per Package (Purchasing)
            // 'VatGroupPu' => $request['VatGroupPu'], //    Purchase Tax Definition

            // 'CardCode' => $request['CardCode'], //Preffered Vendor
            // //sales
            'SUoMEntry' => $salesUom ? $salesUom->id : null, //Sales UoM Code
            'SalUnitMsr' => $salesUom ? $salesUom->UomName : null, // Sales UoM Name
            'NumInSale' => $val[7], // Items Per Sales Unit
            // 'SalPackMsr' => $request['SalPackMsr'], //Packaging Uom Namep
            // 'SalPackUn' => $request['SalPackUn'], //Quantity Per Package

            // 'SVolume' => $request['SVolume'],
            // 'VatGourpSa' => $request['VatGourpSa'], //    Sales Tax Definition

            // //inventry
            // 'EvalSystem' => $request['EvalSystem'],
            // 'GLMethod' => $request['GLMethod'],
            'InvntryUom' => $salesUom ? $salesUom->UomName : null, // Uom Name
            'IUoMEntry' => $salesUom ? $salesUom->id : null, // Uom Code
            // 'CntUnitMsr' => $request['CntUnitMsr'],
            // 'NumInCnt' => $request['NumInCnt'],
            // 'INUoMEntry' => $request['INUoMEntry'],
        ]);

        $priceListData = [
            'Price' => 0,
            'Currency' => 1,
        ];

        (new InventoryService())->ItemMasterDataService($newItem->id, $priceListData);
    }
}
