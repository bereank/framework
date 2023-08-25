<?php

namespace Leysco100\Inventory\Services;

use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;

/**
 * Inventory Services
 */
class InventoryService
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

    public function dispatchEffectOnOrder($BaseType, $Quantity, $BaseEntry)
    {
        $BaseTable = APDI::with('pdi1')
            ->where('ObjectID', $BaseType)
            ->first();

        $rowData =  $BaseTable->pdi1[0]['ChildTable']::where('id', $BaseEntry)->firstOrfail();

        $details = [
            'OpenQty' =>  $rowData->OpenQty - $Quantity
        ];
        $BaseTable->pdi1[0]['ChildTable']::where('id', $BaseEntry)->update($details);
        return $rowData;
    }
}
