<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBTL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OILM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;

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

        /**
         * Mapping Req Name
         */

        if ($data['ObjType'] == 205) {
            if ($data['ReqType'] == 12) {
                $data['ReqName']  = User::where('id', $data['Requester'])->value('name');
            }

            if ($data['ReqType'] == 171) {
                $employee = OHEM::where('id', $data['Requester'])->first();
                $data['ReqName']  = $employee->firstName . " " . $employee->lastName;
            }
        }

        // Document lines defaulting
        if (array_key_exists('document_lines', $data) && !empty($data['document_lines'])) {
            $documentLines = $data['document_lines'];
            foreach ($documentLines as $key => $line) {
                $lineNum =   $key;
                if (data_get($line, 'ItemCode')) {
                    $itemDetails = OITM::where('ItemCode', $line['ItemCode'])->first();
                    if (!data_get($line, 'Dscription')) {
                        if ($itemDetails) {
                            $documentLines[$key]['Dscription'] = $itemDetails['Dscription'];
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
                        if (isset($itemDetails) && $itemDetails) {
                            $documentLines[$key]['WhsCode'] = $itemDetails['DfltWH'];
                        } else {
                            $gen_setting = OADM::where('id', 1)->first();
                            if ($gen_setting) {
                                $documentLines[$key]['WhsCode'] = $gen_setting->DfltWhs;
                            }
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
    public function validateFields($docData, $ObjType)
    {
        //If Base Type Exist
        if (isset($docData['BaseType']) && isset($docData['BaseEntry'])) {
            $generalSettings = OADM::where('id', 1)->value('copyToUnsyncDocs');
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $docData['BaseType'])
                ->first();
            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $docData['BaseEntry'])
                ->first();
            $docData['CardCode'] = $baseDocHeader->CardCode;
            if ($generalSettings == 1 && $baseDocHeader->ExtRef == null) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Copy to is Disable for Documents Pending syncing ");
            }
            if ($baseDocHeader->DocStatus == "C") {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Copying to not Possible, Base Document is closed");
            }
        }

        $ObjType = (int) $docData['ObjType'];

        $docData['saveToDraft']  = false;

        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Not found document with objtype " . $ObjType);
        }
        /**
         * Check if The Item has External Approval
         */
        if ($TargetTables->hasExtApproval == 1) {
            $docData['saveToDraft'] = true;
            $TargetTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }

        if (empty($docData['CardCode']) && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Customer is Required");
        } else {
            $customerDetails = OCRD::where('CardCode', $docData['CardCode'])->first();

            if (!$customerDetails) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Customer Does Not Exist");
            }
        }


        if (array_key_exists('DiscPrcnt', $docData) && $docData['DiscPrcnt'] > 100) {
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
        }

        if (!$docData['DocDueDate'] && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Delivery Date Required");
        }

        if ($ObjType == 205) {
            if (!$docData['ReqType']) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Request Type is required");
            }

            if (!$docData['Requester']) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Requester is required");
            }

            if (!$docData['ReqDate']) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Required date is required");
            }
        }

        if (!array_key_exists('document_lines', $docData) || count($docData['document_lines']) <= 0) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("Items is required");
        } else {
            $this->documentRowValidation($docData, $ObjType);

            return $docData;
        }
    }



    /**
     * Validating Document Row
     */
    public function documentRowValidation($docData, $ObjType)
    {
        $document_lines = $docData['document_lines'];

        foreach ($document_lines as $key => $value) {

            if (!isset($value['Dscription'])) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Dscription id Required for item:");
            }

            /**
             * Item VALIDATIONS
             */

            if ($docData['DocType'] == "I") {
                if (!isset($value["ItemCode"])) {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Item Is Required !");
                }
                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                if (!$product) {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Items Does Not Exist");
                }

                if (array_key_exists('DiscPrcnt', $value) && $value['DiscPrcnt'] > 0 && $product->QryGroup61 == "Y") {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Following Item Does not allow discount:" . $value['Dscription']);
                }
            }

            if (!isset($value['Quantity']) || $value['Quantity'] <= 0) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Invalid quantity   for item:" . $value['Dscription'] ?? "");
            }

            if (!isset($value['Price']) || $value['Price'] < 0) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Invalid price for item:" . $value['Dscription'] ?? "");
            }


            if (!isset($value['WhsCode'])) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Warehouse (WhsCode) Required for item:");
            }

            /**
             * Mapping Req Name
             */

            if ($ObjType == 205) {
                if ($docData['ReqType'] == 12) {
                    $ReqName = User::where('id', $docData['Requester'])->value('name');
                }

                if ($docData['ReqType'] == 171) {
                    $employee = OHEM::where('id', $docData['Requester'])->first();
                    $ReqName = $employee->firstName . " " . $employee->lastName;
                }
            }

            $checkStockAvailabilty = false;

            if (isset($docData['BaseType'])) {
                if (($ObjType == 13 && $docData['BaseType'] != 15) || $ObjType == 15) {
                    $checkStockAvailabilty = true;
                }
            }

            if (array_key_exists('DiscPrcnt', $value) && $value['DiscPrcnt'] > 100) {
                return (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
            }
            if (!isset($value['TaxCode'])) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Select Tax Code for item " . $value['ItemCode'] ?? "");
            }
            // If Not Sales Order the Inventory Quantities should be Greater

            if ($checkStockAvailabilty) {

                if ($product->InvntItem == "Y") {
                    $inventoryDetails = OITW::where('ItemCode',  $value['ItemCode'])
                        ->where('WhsCode', $value['WhsCode'])
                        ->first();

                    if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                        return (new ApiResponseService())
                            ->apiSuccessAbortProcessResponse("Insufficient stock for item:" . $value['Dscription']);
                    }
                }
            }
            $value['ManSerNum'] = false;
            //Serial Number Validations
            if ($product->ManSerNum == "Y") {
                if ($ObjType == 14 || $ObjType == 16 || $docData['saveToDraft'] = true) {
                    if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                    } else {
                        $value['ManSerNum'] = true;
                    }
                }

                if ($ObjType == 15) {
                    if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                    } else {
                        $value['ManSerNum'] = true;
                    }
                }

                if ($ObjType == 13 && $value['BaseType'] != 15) {
                    if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                    } else {
                        $value['ManSerNum'] = true;
                    }
                }
            }
        }
    }


    public function createDoc($data, $TargetTables, $ObjType)
    {

        DB::connection("tenant")->beginTransaction();
        //If Base Type Exist
        if (isset($data['BaseType']) && isset($data['BaseEntry'])) {
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $data['BaseType'])
                ->first();

            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $data['BaseEntry'])
                ->first();
        }
        try {
            $NewDocDetails = [
                'ObjType' => $data['ObjType']  ?? null,
                'DocType' => $data['DocType']  ?? null,
                'DocNum' => $data['DocNum'] ?? null,
                'Series' => $data['Series']  ?? null,
                'CardCode' => $data['CardCode'] ?? null,
                'Requester' => $data['Requester']  ?? null,
                'ReqName' => $data['ReqName'] ?? null,
                'ReqType' => $data['ReqType']  ?? null,
                'Department' => $data['Department']  ?? null,
                'CardName' => $data['CardName'] ?? null,
                'SlpCode' => $data['SlpCode']  ?? null,  // Sales Pipe Line
                'OwnerCode' => $data['OwnerCode']  ?? null, //Owner Code
                'NumAtCard' => $data['NumAtCard'] ?? null,
                'CurSource' => $data['CurSource']  ?? null,
                'DocTotal' => $data['DocTotal']  ?? null,
                'VatSum' => $data['VatSum'] ?? 0,
                'DocDate' => $data['DocDate']  ?? null, //PostingDate
                'TaxDate' => $data['TaxDate']  ?? null, //Document Date
                'DocDueDate' => $data['DocDueDate']  ?? null, // Delivery Date
                'ReqDate' => $data['DocDueDate']  ?? null,
                'CntctCode' => $data['CntctCode']  ?? null, //Contact Person
                'AgrNo' => $data['AgrNo']  ?? null,
                'LicTradNum' => $data['LicTradNum']  ?? null,
                'BaseEntry' => $data['BaseEntry'] ?? null, //BaseKey
                'BaseType' => $data['BaseType'] ?? "1", //BaseKey
                'UserSign' => $data['UserSign'] ?? null,
                'Ref2' => $data['Ref2'] ?? null, // Ref2
                'GroupNum' => $data['GroupNum'] ?? null, //[Price List]
                'ToWhsCode' => $data['ToWhsCode'] ?? null, //To Warehouse Code
                //SeriesDocument
                'DiscPrcnt' => $data['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' => $data['DiscSum']  ?? null, // Discount Sum
                'BPLId' => $data['BPLId']  ?? null,
                'Comments' => $data['Comments']  ?? null, //comments
                'NumAtCard2' => $data['NumAtCard2']  ?? null,
                'JrnlMemo' => $data['JrnlMemo']  ?? null, // Journal Remarks
                'UseShpdGd' => $data['UseShpdGd'] ?? "N",
                'Rounding' => $data['Rounding'] ?? "N",
                'RoundDif' => $data['RoundDif'] ?? 0,
                'DataSource' => "I",
                'ExtRef' => $data['ExtRef'] ?? null,
                'ExtRefDocNum' => $data['ExtRefDocNum'] ?? null,
                'ExtDocTotal' => 0,
            ];

            $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetails));

            $newDoc->save();
            if (!empty($data['udfs'])) {
                $headerUDF = [];
                foreach ($data['udfs'] as $key => $value) {
                    $headerUDF[$key] = $value;
                }
                // Update the model udf's 
                $newDoc->update($headerUDF);
            }
            // Document Rows
            $documentRows = [];
            foreach ($data['document_lines'] as $key => $value) {
                $LineNum = $key;
                $rowdetails = [
                    'DocEntry' => $newDoc->id,
                    'OwnerCode' => $data['OwnerCode'], //Owner Code
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $value['ItemCode'] ?? null,
                    'Dscription' => $value['Dscription'] ?? null, // Item Description
                    'SerialNum' => $value['SerialNum'] ?? null, //    Serial No.
                    'Quantity' => $value['Quantity'] ?? 1, //    Quantity
                    'DelivrdQty' => $value['DelivrdQty'] ?? null, //    Delivered Qty
                    'InvQty' => $value['InvQty'] ?? null, //   Qty(Inventory UoM)
                    'OpenInvQty' => $value['OpenInvQty'] ?? null, //Open Inv. Qty ------
                    'PackQty' => $value['PackQty'] ?? null, //    No. of Packages
                    'Price' => $value['Price'] ?? 0, //    Price After Discount
                    'DiscPrcnt' => $value['DiscPrcnt'] ?? 0, //    Discount %
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : 0, //    Rate
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                    'PriceAfVAT' => $value['PriceAfVAT'] ?? 0, //       Gross Price after Discount
                    'PriceBefDi' => $value['PriceBefDi'] ?? 0, // Unit Price
                    'LineTotal' => $value['LineTotal'] ?? $value['Price'], //    Total (LC)
                    'WhsCode' => $value['WhsCode'] ?? null, //    Warehouse Code
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                    'SlpCode' => $data['SlpCode'] ?? null, //    Sales Employee
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                    'OcrCode' => $value['OcrCode'] ?? null, //    Dimension 1
                    'OcrCode2' => $value['OcrCode2'] ?? null, //    Dimension 2
                    'OcrCode3' => $value['OcrCode3'] ?? null, //    Dimension 3
                    'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                    'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                    'CogsOcrCod' => $value['OcrCode'] ?? null,
                    'CogsOcrCo2' => $value['OcrCode2'] ?? null,
                    'CogsOcrCo3' => $value['OcrCode3'] ?? null,
                    'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                    'CogsOcrCo5' => $value['OcrCode5'] ?? null,

                    'BaseType' => $value['BaseType'] ?? null, //    Base Type
                    'BaseRef' => $value['BaseRef'] ?? null, //    Base Ref.
                    'BaseEntry' => $value['BaseEntry'] ?? null, //    Base Key
                    'BaseLine' => $value['BaseLine'] ?? null, //    Base Row
                    'VatSum' => $value['VatSum'] ?? 0, //    Tax Amount (LC)

                    'UomCode' => $value['UomCode'] ?? null, //    UoM Code
                    'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null, //    UoM Name
                    'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                    'GTotal' => $value['GTotal'] ?? 0, //    Gross Total

                    //Inventory Transaction  Value
                    'PQTReqDate' => $data['ReqDate'] ?? null,
                    'FromWhsCod' => $value['FromWhsCod'] ?? null, // // From Warehouse Code
                    'BPLId' => $data['BPLId'] ?? null,
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                    'StockPrice' => $data['StockPrice'] ?? 0,
                    'NoInvtryMv' => $value['NoInvtryMv'] ?? "N",

                    //Weight
                    'Weight1' => $data['Weight1'] ?? 0,

                ];

                $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
                $rowItems->save();

                //bin allocations
                // if (array_key_exists('bin_allocation', $value)) {
                //     $FromBinCod =    $value['FromBinCod'] ?? null;

                //     $data = (new InventoryService())->binAllocations(
                //         $value['ItemCode'],
                //         $value['Quantity'],
                //         $value['bin_allocation'],
                //         $value['ToWhsCode'],
                //         $FromBinCod
                //     );
                //     $oilm =  OILM::create([
                //         'DocEntry' => $newDoc->id,
                //         'TransType' => $ObjType,
                //         'BaseType' => $data['BaseType'] ?? null,
                //         'DocLineNum' => $LineNum,
                //         'Quantity' => $value['Quantity'],
                //         'ItemCode' => $value['ItemCode'],
                //         'UserSign' => Auth::user()->id,
                //         'BPCardCode' => $data['CardCode'] ?? null,
                //         'SnBType' => $value['ManSerNum'] == "Y" ? 1 : 0,
                //         'SlpCode' => $data['SlpCode'] ?? null,
                //     ]);
                //     OBTL::create([
                //         'MessageID' => $oilm->MessageID,
                //         'BinAbs' => $data->id,
                //         'SnBMDAbs' => NULL,
                //         'Quantity' => $value['Quantity'],
                //         'ITLEntry' => NULL,
                //     ]);
                // }

                $lineUDF = [];
                if (!empty($value['udfs'])) {
                    foreach ($value['udfs'] as  $key => $value) {
                        $lineUDF[$key] = $value;
                    }
                    // Update the model udf's 
                    $rowItems->update($lineUDF);
                }
                if ($data['DocType'] == "I" && isset($value['ManSerNum']) && $value['ManSerNum'] == "Y") {

                    $saveSerialDetails = false;
                    if ($data['ObjType'] == 14 || $data['ObjType'] == 16 || $data['ObjType'] == 17) {
                        $saveSerialDetails = true;
                    }
                    if ($data['ObjType'] == 15) {
                        $saveSerialDetails = true;
                    }
                    if ($data['ObjType'] == 13 && isset($value['BaseType']) && $value['BaseType'] != 15) {
                        $saveSerialDetails = true;
                    }
                    if ($saveSerialDetails) {
                        foreach ($value['SerialNumbers'] as $key => $serial) {
                            Log::info(["serials" => $serial]);
                            $LineNum = $key;
                            SRI1::create([
                                "ItemCode" => $value['ItemCode'],
                                "SysSerial" => $serial['SysNumber'] ?? $serial['SysSerial'],
                                "LineNum" => $rowItems->id,
                                "BaseType" => $data['saveToDraft'] ? 112 : $ObjType,
                                "BaseEntry" => $newDoc->id,
                                "CardCode" => $data['CardCode'],
                                "CardName" => $data['CardName'],
                                "WhsCode" => $value['WhsCode'],
                                "ItemName" => $value['Dscription'],
                            ]);
                        }
                    }
                }

                if (isset($data['BaseType']) && isset($data['BaseEntry'])) {
                    $baseDocHeader->update([
                        'DocStatus' => "C",
                    ]);
                }
                array_push($documentRows, $rowItems);
            }

            (new SystemDefaults())->updateNextNumberNumberingSeries($data['Series']);

            DB::connection("tenant")->commit();
            $newDoc['document_lines'] = $documentRows;
            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Process failed, Server Error" . $th->getMessage());
        }
    }
}
