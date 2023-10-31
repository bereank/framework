<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;

class MarketingDocumentService
{
    public function fieldsDefaulting($data)
    {

        // Document header defaulting
        $user_id = Auth::user()->id;
        $user_data = User::with('oudg')->where('id', $user_id)->first();

        if (!(data_get($data, 'SlpCode'))) {
            $data['SlpCode'] = $user_data->oudg->SalePerson ?? null;
        }
        if (!(data_get($data, 'OwnerCode'))) {
            $data['OwnerCode'] = $user_data->EmpID ?? null;
        }
        if (!(data_get($data, 'DocDate'))) {
            $data['DocDate'] =  Carbon::now()->format('Y-m-d');
        }
        if (!(data_get($data, 'UserSign'))) {
            $data['UserSign'] =   $user_data->id;
        }
        if (!(data_get($data, 'DataSource'))) {
            $data['DataSource'] = 'I';
        }

        if ((data_get($data, 'CardCode'))) {
            $customerDetails = OCRD::where('CardCode', $data['CardCode'])->first();
            if (!(data_get($data, 'CardName'))) {
                if ($customerDetails) {
                    $data['CardName'] = $customerDetails['CardName'];
                }
            }
        }
        if (!(data_get($data, 'DocNum'))) {
            $Numbering = (new DocumentsService())
                ->getNumSerieByObjectId($data['ObjType']);
            $data['DocNum'] = $Numbering['NextNumber'];
            $data['Series'] = $Numbering['id'];
        }

        // Document lines defaulting
        if ($data['document_lines']) {
            $documentLines = $data['document_lines'];
            foreach ($documentLines as $key => $line) {
                $lineNum =   $key;
                if (data_get($line, 'ItemCode')) {
                    $itemDetails = OITM::where('ItemCode', $line['ItemCode'])->first();
                    if (!data_get($line, 'ItemName')) {
                        if ($itemDetails) {
                            $documentLines[$key]['Dscription'] = $itemDetails['ItemName'];
                        }
                    }
                    if ($itemDetails) {
                        $StockPrice = $itemDetails->AvgPrice;
                        $documentLines[$key]['StockPrice'] = $StockPrice;
                    }
                    if (data_get($line, 'Quantity')) {
                        if ($itemDetails) {
                            $Weight1 = $itemDetails->SWeight1 * $line['Quantity'];
                            $documentLines[$key]['Weight1'] = $Weight1;
                        }
                    }
                }
                if (!(data_get($line, 'WhsCode'))) {
                    if ($user_data->oudg->Warehouse) {
                        $documentLines[$key]['WhsCode'] =  $user_data->oudg->Warehouse;
                    } else {
                        if ($itemDetails) {
                            $documentLines[$key]['WhsCode'] = $itemDetails['DfltWH'];
                        }
                    }
                }
                if (!(data_get($line, 'LineNum'))) {
                    $documentLines[$key]['LineNum'] = ++$lineNum;
                }
                if (!(data_get($line, 'SlpCode'))) {
                    $documentLines[$key]['SlpCode'] = $user_data->oudg->SalePerson ?? null;
                }
                if (!(data_get($line, 'OwnerCode'))) {
                    $documentLines[$key]['OwnerCode'] = $user_data->EmpID ?? null;
                }
            }
            $data['document_lines'] = $documentLines;
        }


        return $data;
    }


    public function createDoc($data, $TargetTables, $ObjType)
    {
        DB::connection("tenant")->beginTransaction();
        try {

            $header_data = $data['header_data'];
            $header_data['ObjType'] = $ObjType;
            $newDoc =  (new $TargetTables->ObjectHeaderTable)->fill($header_data);
            $newDoc->save();
            Log::info($newDoc);
            foreach ($data['document_lines'] as $key => $value) {
                Log::info($value);
                $value['DocEntry'] = $newDoc->id;
                $rowItems = (new $TargetTables->pdi1[0]['ChildTable'])->fill($value);
                $rowItems->save();
            }

            (new SystemDefaults())->updateNextNumberNumberingSeries($header_data['Series']);

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())
                ->apiFailedResponseService("Process failed, Server Error", $th->getMessage());
        }
    }
}
