<?php

namespace Leysco100\Shared\Actions;

use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;




class TransactionInventoryEffectAction
{

    public function transactionInventoryEffect($ObjType, $docEntry)
    {

        return "DISABLED";
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            //            ->where('ObjectID', 112)
            ->first();

        $data = $DocumentTables->ObjectHeaderTable::with('document_lines')
            ->where('ObjType', $ObjType)
            ->where('id', $docEntry)
            ->first();

        //        dd($data);

        $documentRows = $data["document_lines"];
        foreach ($documentRows as $key => $val) {
            //            dd($val);
            $postedStock = $val->Quantity;
            $itemCode = $val->ItemCode;
            $whsCode = $val->WhsCode;
            $itemMasterData = OITM::where('ItemCode', $itemCode)->first();

            $itemOITW = OITW::where('ItemCode', $itemCode)
                ->where('WhsCode', $whsCode)
                ->first();
            $currentIsCommited =  $itemOITW->IsCommited;

            if ($ObjType ==  17) {
                $itemOITW->update([
                    'IsCommited' => $currentIsCommited + $postedStock
                ]);

                if ($itemMasterData->ManSerNum == "Y") {
                    $sri1Data = SRI1::where('ItemCode', $itemCode)
                        ->where('BaseType', $ObjType)
                        ->where('BaseEntry', $data->id)
                        ->where('LineNum', $val->id)
                        ->get();

                    if ($sri1Data) {
                        //Fetching OSRN
                        foreach ($sri1Data as $sri1) {
                            $osrnData = OSRN::where('ItemCode', $itemCode)
                                ->where('SysNumber', $sri1->SysSerial)
                                ->first();

                            $osrnData->update([
                                'Status' => 2 //Allocated
                            ]);

                            $osrqData = OSRQ::where('ItemCode', $itemCode)
                                ->where('SysNumber', $osrnData->SysNumber)
                                ->where('MdAbsEntry', $osrnData->AbsEntry)
                                ->where('WhsCode', $whsCode)
                                ->first();
                            $osrqData->update([
                                'CommitQty' => 1 //Allocated
                            ]);
                        }
                    }
                }
            }
            //            if ($ObjType ==  13) {
            //                $itemOITW->update([
            //                    'IsCommited' => $currentIsCommited + $postedStock
            //                ]);
            //
            //                if ($itemMasterData->ManSerNum == "Y") {
            //                    $sri1Data = SRI1::where('ItemCode', $itemCode)
            //                        ->where('BaseType', $ObjType)
            //                        ->where('BaseEntry', $data->id)
            //                        ->where('LineNum', $val->id)
            //                        ->get();
            //
            //                    if ($sri1Data) {
            //                        //Fetching OSRN
            //                        foreach ($sri1Data as $sri1){
            //                            $osrnData = OSRN::where('ItemCode', $itemCode)
            //                                ->where('SysNumber', $sri1->SysSerial)
            //                                ->first();
            //
            //                            $osrnData->update([
            //                                'Status' => 2 //Allocated
            //                            ]);
            //
            //                            $osrqData = OSRQ::where('ItemCode', $itemCode)
            //                                ->where('SysNumber', $osrnData->SysNumber)
            //                                ->where('MdAbsEntry', $osrnData->AbsEntry)
            //                                ->where('WhsCode', $whsCode)
            //                                ->first();
            //                            $osrqData->update([
            //                                'Quantity' => 0
            //                            ]);
            //                        }
            //                    }
            //                }
            //            }


        }

        return $data;
    }
}
