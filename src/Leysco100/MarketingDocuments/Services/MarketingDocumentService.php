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
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
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
        if ($data['document_lines']) {
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
    public function validateFields($docData, $ObjType)
    {
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        if (empty($docData['CardCode']) && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Customer is Required");
        } else {
            $customerDetails = OCRD::where('CardCode', $docData['CardCode'])->first();

            if (!$customerDetails) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Customer Does Not Exist");
            }
        }


        if ($docData['DiscPrcnt'] > 100) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Invalid Discount Percentage");
        }

        if (!$docData['DocDueDate'] && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Delivery Date Required");
        }

        if ($ObjType == 205) {
            if (!$docData['ReqType']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Request Type is required");
            }

            if (!$docData['Requester']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Requester is required");
            }

            if (!$docData['ReqDate']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Required date is required");
            }
        }

        if (count($docData['document_lines']) <= 0) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Items is required");
        }
        $this->documentRowValidation($docData, $ObjType);

        return $docData;
    }



    /**
     * Validating Document Row
     */
    public function documentRowValidation($docData, $ObjType)
    {
        $document_lines = $docData['document_lines'];

        foreach ($document_lines as $key => $value) {

            if ($value['Quantity'] <= 0) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Invalid quantity   for item:" . $value['Dscription']);
            }

            if ($value['Price'] < 0) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Invalid price for item:" . $value['Dscription']);
            }

            if (!$value['WhsCode']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Warehouse Required");
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

            if (($ObjType == 13 && $docData['BaseType'] != 15) || $ObjType == 15) {
                $checkStockAvailabilty = true;
            }

            /**
             * Item VALIDATIONS
             */

            if ($docData['DocType'] == "I") {
                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                if (!$product) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Items Required");
                }

                if (array_key_exists('DiscPrcnt', $value) && $value['DiscPrcnt'] > 0 && $product->QryGroup61 == "Y") {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Following Item Does not allow discount:" . $value['Dscription']);
                }
            }

            if (array_key_exists('DiscPrcnt', $value) && $value['DiscPrcnt'] > 100) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
            }
            if (!isset($value['TaxCode'])) {
                (new ApiResponseService())
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
                            ->apiFailedResponseService("Insufficient stock for item:" . $value['Dscription']);
                    }
                }
            }
        }
    }


    public function createDoc($data, $TargetTables, $ObjType)
    {
        DB::connection("tenant")->beginTransaction();
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
                'BaseEntry' => $data['BaseEntry'] ? $data['BaseEntry'] : null, //BaseKey
                'BaseType' => $data['BaseType'] ?? "1", //BaseKey
                'UserSign' => $data['UserSign'] ?? null,
                'Ref2' => $data['Ref2'] ? $data['Ref2'] : null, // Ref2
                'GroupNum' => $data['GroupNum'] ? $data['GroupNum'] : null, //[Price List]
                'ToWhsCode' => $data['ToWhsCode'] ? $data['ToWhsCode'] : null, //To Warehouse Code
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
                    'SlpCode' => $data['SlpCode'], //    Sales Employee
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
            }
            $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
            $rowItems->save();
            (new SystemDefaults())->updateNextNumberNumberingSeries($data['Series']);

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
