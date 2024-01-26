<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Leysco100\Shared\Models\OUQR;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\CSHS;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBTL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OILM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\MarketingDocuments\Http\Controllers\API\PriceCalculationController;

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
        if (array_key_exists('CardCode', $data)) {
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

                        //defaulting item dimensions
                        $dimensions =     (new PriceCalculationController())->getItemDefaultDimensions($itemDetails->id);
                        $documentLines[$key]['OcrCode'] = isset($line['OcrCode']) && !empty($line['OcrCode']) ? $line['OcrCode'] : $dimensions['OcrCode'];
                        $documentLines[$key]['OcrCode2'] = isset($line['OcrCode2']) && !empty($line['OcrCode2']) ? $line['OcrCode2'] : $dimensions['OcrCode2'];
                        $documentLines[$key]['OcrCode3'] = isset($line['OcrCode3']) && !empty($line['OcrCode3']) ? $line['OcrCode3'] : $dimensions['OcrCode3'];
                        $documentLines[$key]['U_AllowDisc'] = $dimensions['U_AllowDisc'] ?? null;
                        $documentLines[$key]['OcrCode4'] = isset($line['OcrCode4']) && !empty($line['OcrCode4']) ? $line['OcrCode4'] : (isset($dimensions['OcrCode4']) ? $dimensions['OcrCode4'] : null);
                        $documentLines[$key]['OcrCode5'] = isset($line['OcrCode5']) && !empty($line['OcrCode5']) ? $line['OcrCode5'] : (isset($dimensions['OcrCode5']) ? $dimensions['OcrCode5'] : null);

                        // if ($user_data->oudg->SellFromBin && $data['ObjType'] == 13 && empty($value['bin_allocation'])) {
                        //     if (isset($line['OcrCode4']) || isset($dimensions['OcrCode4'])) {
                        //         $lineOcrCode4 =  $line['OcrCode4'] ?? $dimensions['OcrCode4'];
                        //         $defaults = OUDG::where('CogsOcrCo4',  $lineOcrCode4)->first();
                        //         $obin = OBIN::where('id', $defaults->DftBinLoc)->first();
                        //         if ($defaults->DftBinLoc && $obin) {
                        //             $documentLines[$key]['bin_allocation'] =  [
                        //                 [
                        //                     'BinCode' => $obin->BinCode,
                        //                     'QtyVar' =>  $line['Quantity']
                        //                 ]
                        //             ];
                        //         }
                        //     }
                        // }
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
                        $wareHouse =  OWHS::find($user_data->oudg->Warehouse);
                        $documentLines[$key]['WhsCode'] = $wareHouse->WhsCode;
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
                if (!(data_get($line, 'TaxCode'))) {
                    $taxGroup = TaxGroup::where('code', $itemDetails->VatGourpSa)->first();
                    $documentLines[$key]['TaxCode'] = $taxGroup->code ?? null;
                }


                $res =  CSHS::where('ObjType', $data['ObjType'])->first();
                if ($res) {
                    $query =  OUQR::where('id', $res->QueryId)->first();
                    $string = $query->QString;
                    preg_match('/(\d+)(\.\w+)/', $string, $matches);
                    $substring = $matches[0];
                    $number = $matches[1];

                    $matchSubstring = str_replace('.', '', $matches[2]);

                    $replacementValue =   $documentLines[$key][$matchSubstring];

                    $processedString = Str::replace('$[' . $substring . ']', '"' . $replacementValue . '"', $string);

                    $result = DB::connection('tenant')->select($processedString);
                    //   Log::info($result);
                    // $documentLines[$key]['Dscription'] = $result;
                    if (is_array($result)) {
                        if (!empty($result)) {
                            $headers =  array_values((array)$result[0]);
                            $documentLines[$key][$res->ItemID] =  $headers[0];
                        }
                    }
                    // Log::info([$processedString, $res->ItemID, $headers, $number, $matchSubstring, $line]);
                }

                if (!(data_get($line, 'bin_allocation')) &&  data_get($documentLines[$key], 'ToBinCod')) {
                    Log::info("CREATE BIN ALLOC");
                    $documentLines[$key]['bin_allocation'] =  [
                        [
                            'BinCode' => $documentLines[$key]['ToBinCod'],
                            'QtyVar' =>  $line['Quantity']
                        ]
                    ];
                }
            }
            $data['document_lines'] = $documentLines;
            //  Log::info($data['document_lines']);
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
        if (!array_key_exists('DocTotal', $docData)) {
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Document Total is required.");

            if (!$docData['DocTotal'] <= 0) {
                return (new ApiResponseService())
                    ->apiSuccessAbortProcessResponse("Document Total Cannot be Less than 1.");
            }
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



            /**
             * Item VALIDATIONS
             */

            if ($docData['DocType'] == "I") {
                if (!isset($value["ItemCode"])) {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Item Code Is Required");
                }
                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                if (!$product) {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Items Does Not Exist");
                }

                if (array_key_exists('DiscPrcnt', $value) && $value['DiscPrcnt'] > 0 && $product->QryGroup61 == "Y") {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Following Item Does not allow discount:" . $value['Dscription']);
                }
                if (!isset($value['Dscription'])) {
                    return (new ApiResponseService())
                        ->apiSuccessAbortProcessResponse("Dscription is Required for item:" .
                            array_key_exists('ItemCode', $value) ? $value["ItemCode"] : null);
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
            //Validate Bin-locations
            if (array_key_exists('bin_allocation', $value) && !empty($value['bin_allocation'])) {
                foreach ($value['bin_allocation'] as $key => $BinVal) {
                    if (!empty($BinVal)) {
                        $obin = OBIN::where('BinCode', $BinVal['BinCode'])->first();
                        if (!$obin) {
                            return (new ApiResponseService())
                                ->apiNotFoundResponse("Bin Code Does Not Exist");
                        }
                    }
                }
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

                // $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
                // $rowItems->save();

                $result = array_filter($TargetTables->pdi1->toArray(), function ($item) {
                    return $item['ChildType'] === 'document_lines';
                });

                $lineModel = collect($result)->first();

                $rowItems = new $lineModel['ChildTable']($rowdetails);
                $rowItems->save();

                //bin allocations
                if (array_key_exists('bin_allocation', $value)) {
                    $result = array_filter($TargetTables->pdi1->toArray(), function ($item) {
                        return $item['ChildType'] === 'bin_allocations';
                    });
                    $lineModel = collect($result)->first();

                    $FromBinCod =    $value['WhsCode'] ?? null;

                    foreach ($value['bin_allocation'] as $key => $BinVal) {
                        $SubLineNum = ++$key;
                        $obin = OBIN::where('BinCode', $BinVal['BinCode'])->first();

                        $bindata = $lineModel['ChildTable']::create([
                            'DocEntry' => $newDoc->id,
                            'BinAllocSe' => $LineNum,
                            'LineNum' => $LineNum,
                            'SubLineNum' => $SubLineNum,
                            'SnBType' => null,
                            'SnBMDAbs' => null,
                            'BinAbs' =>  $obin->id,
                            'Quantity' =>  $BinVal['QtyVar'],
                            'ItemCode' => $value['ItemCode'],
                            'WhsCode' =>  $value['WhsCode'] ?? $obin->WhsCode,
                            'ObjType' => $ObjType,
                            'AllowNeg' => 'N',
                            'BinActTyp' => 1
                        ]);

                        $resdata =    (new InventoryService())->binAllocations(
                            $ObjType,
                            $value['ItemCode'],
                            $BinVal,
                            $WhsCode = null,
                            $FromBinCod
                        );
                    }
                }



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
                                "CardCode" => $data['CardCode'] ?? null,
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

            //Record Payment data
            if (array_key_exists('payments', $data) && !empty($data['payments']) && $ObjType == 13 &&  $ObjType == 112) {
                foreach ($data['payments'] as $payment) {
                    //                $storedProcedureResponse = null;
                    if ($ObjType == 13) {
                        $newPayment = (new BankingDocumentService())->processIncomingPayment($newDoc, $payment);
                        //                        $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(140, "A", $newPayment->id);
                    } else {
                        $newPayment = (new BankingDocumentService())->processDraftIncomingPayment($newDoc, $payment);
                        //                        $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(24, "A", $newPayment->id);
                    }
                    //                if ($storedProcedureResponse) {
                    //                    if ($storedProcedureResponse->error != 0) {
                    //                        return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
                    //                    }
                    //                }
                }
            }
            DB::connection("tenant")->commit();
            $newDoc['document_lines'] = $documentRows;
            return $newDoc;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())
                ->apiSuccessAbortProcessResponse("Process failed, Server Error" . $th->getMessage());
        }
    }
}
