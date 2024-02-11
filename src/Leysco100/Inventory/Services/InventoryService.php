<?php

namespace Leysco100\Inventory\Services;

use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBTL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OIBQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OILM;
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

    public function binQuantities(
        $value,
        $lineModel,
        $newDocID,
        $LineNum,
        $ItemCode,
        $ToWhsCode,
        $ObjType,
        $FromBinCod,
        $docData
    ) {
        foreach ($value['bin_allocation'] as $key => $BinVal) {
            if (!empty($BinVal)) {
                $SubLineNum = ++$key;
                $obin = OBIN::where('BinCode', $BinVal['BinCode'])->first();
                $bindata = $lineModel['ChildTable']::create([
                    'DocEntry' => $newDocID,
                    'BinAllocSe' => $LineNum,
                    'LineNum' => $LineNum,
                    'SubLineNum' => $SubLineNum,
                    'SnBType' => null,
                    'SnBMDAbs' => null,
                    'BinAbs' =>  $obin->id,
                    'Quantity' =>  $BinVal['QtyVar'],
                    'ItemCode' => $ItemCode,
                    'WhsCode' =>  $ToWhsCode,
                    'ObjType' =>  $ObjType,
                    'AllowNeg' => 'N',
                    'BinActTyp' => 1
                ]);

                $res =   $this->binAllocations(
                    $ObjType,
                    $ItemCode,
                    $BinVal,
                    $ToWhsCode,
                    $FromBinCod,
                    $docData
                );
                return $res;
            }
        }
    }

    public function binAllocations($ObjType, $ItemCode, $bin_allocation, $ToWhsCode, $FromBinCod, $docData)
    {

        $obin = OBIN::where('BinCode', $bin_allocation['BinCode'])->first();

        $oibq = OIBQ::where('ItemCode', $ItemCode)
            ->where('BinAbs', $obin->id)
            ->first();

        if ($FromBinCod) {
            $fromobin = OBIN::where('BinCode', $FromBinCod)->first();

            if ($fromobin) {
                $fromoibq = OIBQ::where('ItemCode', $ItemCode)
                    ->where('BinAbs', $fromobin->id)
                    ->first();
                if ($fromoibq) {
                    $fromoibq->update([
                        "OnHandQty" => $fromoibq->OnHandQty - $bin_allocation['QtyVar']
                    ]);
                }
            }
        }
        if (!$oibq) {
            if ($ObjType == 67) {
                OIBQ::create([
                    'ItemCode' => $ItemCode,
                    'BinAbs' => $obin->id,
                    'OnHandQty' => $bin_allocation['QtyVar'],
                    'ToWhsCode' => $ToWhsCode ?? null,
                ]);
            }
        } else {
            if ($ObjType == 67) {
                $oibq->update([
                    "OnHandQty" => $bin_allocation['QtyVar'] + $oibq->OnHandQty
                ]);
            }
            $checkStockAvailabilty = false;

            if (($ObjType == 13 || (array_key_exists('BaseType', $docData) &&
                $docData['BaseType'] != 15 && $ObjType == 13)) || $ObjType == 15) {
                $checkStockAvailabilty = true;
                Log::info(["checkStockAvailabilty" => $checkStockAvailabilty]);
            }

            if ($checkStockAvailabilty) {
                $oibq->update([
                    "OnHandQty" => $oibq->OnHandQty - $bin_allocation['QtyVar']
                ]);
            }
        }



        return $obin;
    }
}
