<?php

namespace App\Domains\InventoryAndProduction\Services;

use App\Domains\InventoryAndProduction\Models\ITM1;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OSRQ;
use App\Domains\Marketing\Models\OPLN;

/**
 * Inventory Services
 */
class InventoryProcessService
{
    /**
     *  Creating Price List for the new Item
     * @param Int $ItemCode, Auto Increment ID OITM
     * @param array $PriceLists
     */
    public function ItemMasterDataService($ItemCode, $priceListData)
    {
        // $pricelist = $priceListData['PriceList'];
        $pricelist = OPLN::get();
        foreach ($pricelist as $key => $value) {
            ITM1::updateOrcreate([
                'ItemCode' => $ItemCode,
                'PriceList' => $value->id,
            ], [
                'Price' => $priceListData['Price'] ? $priceListData['Price'] : null,
                'Currency' => 1,
            ]);
        }
        // ITM1::get();
    }

    /*
    |--------------------------------------------------------------------------
    | Increase Is Committed
    |--------------------------------------------------------------------------
    |
     */
    public function IncreaseisCommittedEffect($value)
    {

        //Inventory Effects
        $UpdateOITWDetails = [
            'IsCommited' => OITW::where('ItemCode', $value['ItemCode'])
                ->where('WhsCode', $value['WhsCode'])
                ->value('IsCommited')
             +
            $value['Quantity'],
        ];
        OITW::where('ItemCode', $value['ItemCode'])
            ->where('WhsCode', $value['WhsCode'])
            ->update($UpdateOITWDetails);

        //Updating OITM
        $UpdateOITMDetails = [
            'IsCommited' => OITM::where('ItemCode', $value['ItemCode'])
                ->value('IsCommited')
             +
            $value['Quantity'],
        ];
        OITM::where('ItemCode', $value['ItemCode'])
            ->update($UpdateOITMDetails);
    }
    /*
    |--------------------------------------------------------------------------
    | Decrease Is Committed
    |--------------------------------------------------------------------------
    |
     */
    public function DecreaseisCommittedEffect($value)
    {

        //Inventory Effects
        $UpdateOITWDetails = [
            'IsCommited' => OITW::where('ItemCode', $value['ItemCode'])
                ->where('WhsCode', $value['WhsCode'])
                ->value('IsCommited')
             -
            $value['Quantity'],
        ];
        OITW::where('ItemCode', $value['ItemCode'])
            ->where('WhsCode', $value['WhsCode'])
            ->update($UpdateOITWDetails);

        //Updating OITM
        $UpdateOITMDetails = [
            'IsCommited' => OITM::where('ItemCode', $value['ItemCode'])
                ->value('IsCommited')
             -
            $value['Quantity'],
        ];
        OITM::where('ItemCode', $value['ItemCode'])
            ->update($UpdateOITMDetails);
    }
    /*
    |--------------------------------------------------------------------------
    | Increase Is On Hand
    |--------------------------------------------------------------------------
    |
     */
    public function increaseOnHandEffect($value)
    {
        //Inventory Effects
        $UpdateOITWDetails = [
            'OnHand' => OITW::where('ItemCode', $value['ItemCode'])
                ->where('WhsCode', $value['WhsCode'])
                ->value('OnHand')
             -
            $value['InvQty'],
        ];
        OITW::where('ItemCode', $value['ItemCode'])
            ->where('WhsCode', $value['WhsCode'])
            ->update($UpdateOITWDetails);

        //Updating OITM
        $UpdateOITMDetails = [
            'OnHand' => OITM::where('ItemCode', $value['ItemCode'])
                ->value('OnHand')
             -
            $value['InvQty'],
        ];
        OITM::where('ItemCode', $value['ItemCode'])
            ->update($UpdateOITMDetails);
    }
    /*
    |--------------------------------------------------------------------------
    | Decrease Is On Hand
    |--------------------------------------------------------------------------
    |
     */
    public function decreaseOnHandEffect($value)
    {
        //Inventory Effects
        $UpdateOITWDetails = [
            'OnHand' => OITW::where('ItemCode', $value['ItemCode'])
                ->where('WhsCode', $value['WhsCode'])
                ->value('OnHand')
             -
            $value['InvQty'],
        ];
        OITW::where('ItemCode', $value['ItemCode'])
            ->where('WhsCode', $value['WhsCode'])
            ->update($UpdateOITWDetails);

        //Updating OITM
        $UpdateOITMDetails = [
            'OnHand' => OITM::where('ItemCode', $value['ItemCode'])
                ->value('OnHand')
             -
            $value['InvQty'],
        ];
        OITM::where('ItemCode', $value['ItemCode'])
            ->update($UpdateOITMDetails);
    }

    /**
     *
     */
    public function decreaseSerialNumbers(string $ItemCode, string $WhsCode, int $SysNumber): void
    {
        $osrq = OSRQ::where('ItemCode', $ItemCode)->where('WhsCode', $WhsCode)
            ->where('SysNumber', $SysNumber)
            ->first();

        if ($osrq) {
            $osrq->update([
                'Quantity' => 0,
            ]);
        }
    }
}
