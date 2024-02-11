<?php



namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Leysco100\Shared\Models\OUQR;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\CSHS;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\MarketingDocuments\Models\OATS;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBTL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OIBQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OILM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\WTR19;
use Leysco100\MarketingDocuments\Services\MarketingDocumentService;
use Leysco100\MarketingDocuments\Services\DatabaseValidationServices;
use Leysco100\MarketingDocuments\Http\Controllers\API\PriceCalculationController;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentValidationService;



class InventoryTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDocData(Request $request, $ObjType)
    {
        $isDoc = \Request::get('isDoc');
        $isForPrint = \Request::get('isForPrint');

        $createdBy = Auth::user()->id;
        if (\Request::get('created_by')) {
            $createdBy = \Request::get('created_by');
        }

        $tableObjType = $ObjType;
        if ($isDoc == 0) {
            $tableObjType = 112;
        }

        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $tableObjType)
            ->first();

        // (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'read');

        try {
            $data = $DocumentTables->ObjectHeaderTable::where('ObjType', $ObjType)
                ->with('objecttype', 'branch', 'CreatedBy')
                ->where(function ($q) use ($isDoc) {
                    if ($isDoc == 2) {
                        $q->where('DataSource', "E")->where('DocStatus', "O");
                    }
                })
                ->where(function ($q) use ($isDoc) {
                    if ($isDoc == 0) {
                        $q->where('DocStatus', "O");
                    }
                })
                ->orderBy('id', 'desc')
                ->take(100)
                ->get();
            Log::info($data);
            foreach ($data as $key => $val) {
                $val->isDoc = (int) $isDoc;
                $checkErrors = EOTS::where('ObjType', $tableObjType)
                    ->where('DocEntry', $val->id)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$val->ExtRef) {
                    if ($checkErrors) {
                        $val->withErrors = true;
                        $val->ErrorMessage = $checkErrors->ErrorMessage;
                    }
                }
                if ($checkErrors && $val->ExtRef) {
                    $val->withErrors = false;
                }
                if (!$checkErrors && $val->ExtRef) {
                    $val->withErrors = false;
                }
                if (!$checkErrors && !$val->ExtRef) {
                    $val->withErrors = 'upload';
                }
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ObjType' => 'required',
        ]);

        $user = Auth::user();
        $ObjType = (int) $request['ObjType'];

        $saveToDraft = false;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        /**
         * Check if The Item has External Approval
         */
        if ($TargetTables->hasExtApproval == 1) {
            $saveToDraft = true;
            $TargetTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }

        if (!$request['ToWhsCode']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("To Warehouse Required");
        }

        if (!$request['FromWhsCod']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("From Warehouse Required");
        }
        // Step Default Fields
        $defaulted_data = (new MarketingDocumentService())->fieldsDefaulting($request->all());


        DB::connection("tenant")->beginTransaction();
        try {
            $NewDocDetails = [
                'ObjType' =>  $defaulted_data['ObjType'],
                'DocType' =>  $defaulted_data['DocType'] ?? null,
                'DocNum' => $defaulted_data['DocNum'] ?? null,
                'Series' => $defaulted_data['Series'] ?? null,
                //                'ToWhsCode' => $request['ToWhsCode'] ?? null,
                'Filler' => $defaulted_data['FromWhsCod'] ?? null,
                'SlpCode' => $defaulted_data['SlpCode'] ?? null, // Sales Employee       
                'OwnerCode' => $defaulted_data['OwnerCode'] ?? null, //Owner Code
                'NumAtCard' =>  $defaulted_data['NumAtCard'] ??  null,
                'CurSource' =>  $defaulted_data['CurSource'] ?? null,
                'DocTotal' =>  $defaulted_data['DocTotal'] ?? null,
                'VatSum' =>  $defaulted_data['VatSum'] ?? 0,
                'DocDate' =>  $defaulted_data['DocDate'] ?? null, //PostingDate
                'TaxDate' =>  $defaulted_data['TaxDate'] ?? now(), //Document Date
                'DocDueDate' =>  $defaulted_data['DocDueDate'] ?? now(), // Delivery Date
                'ReqDate' =>  $defaulted_data['DocDueDate'] ?? null,
                'CntctCode' =>  $defaulted_data['CntctCode'] ?? null, //Contact Person
                'AgrNo' =>  $defaulted_data['AgrNo'] ?? null,
                'LicTradNum' =>  $defaulted_data['LicTradNum'] ?? null,
                'BaseEntry' =>  $defaulted_data['BaseEntry'] ??  null, //BaseKey
                'BaseType' =>  $defaulted_data['BaseType'] ??   null, //BaseKey
                'UserSign' => $user->id,
                //Inventory Transaction Values
                'Ref2' =>  $defaulted_data['Ref2'] ?? null, // Ref2
                'GroupNum' =>  $defaulted_data['GroupNum'] ?? null, //[Price List]
                'ToWhsCode' =>  $defaulted_data['ToWhsCode'] ?? null, //To Warehouse Code
                //SeriesDocument
                'DiscPrcnt' =>  $defaulted_data['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' =>  $defaulted_data['DiscSum'] ?? null, // Discount Sum
                'BPLId' =>  $defaulted_data['BPLId'] ?? null,
                'Comments' =>  $defaulted_data['Comments'] ?? null, //comments
                'NumAtCard2' =>  $defaulted_data['NumAtCard2'] ?? null,
                'JrnlMemo' =>  $defaulted_data['JrnlMemo'] ?? null, // Journal Remarks
                'UseShpdGd' =>  $defaulted_data['UseShpdGd'] ?? "N",
                'U_SaleType' =>  $defaulted_data['U_SaleType'] ?? null, // Sale Type
                'U_ServiceCall' =>  $defaulted_data['U_ServiceCall'] ?? null,
                'U_DemoLocation' =>  $defaulted_data['U_DemoLocation'] ?? null,
                'U_Technician' =>  $defaulted_data['U_Technician'] ?? null,
                'U_Location' =>  $defaulted_data['U_Location'] ?? null,
                'U_MpesaRefNo' =>  $defaulted_data['U_MpesaRefNo'] ?? null,
                'U_PCash' =>  $defaulted_data['U_PCash'] ?? null,
                'U_transferType' =>  $defaulted_data['U_transferType'] ?? null,
                'U_SSerialNo' =>  $defaulted_data['U_SSerialNo'] ?? null,
                'U_TypePur' =>  $defaulted_data['U_TypePur'] ?? null,
                'U_NegativeMargin' =>  $defaulted_data['U_NegativeMargin'] ?? null,
                'U_BaseDoc' =>  $defaulted_data['U_BaseDoc'] ?? null,
                'U_SalePipe' => $defaulted_data['U_SalePipe'] ?? null, // Sales Pipe Line
                'U_CashName' =>  $defaulted_data['U_CashName'] ?? null, //Cash Customer  Name
                'U_CashNo' =>  $defaulted_data['U_CashNo'] ?? null, // Cash Customer No
                'U_IDNo' =>  $defaulted_data['U_IDNo'] ?? null, // Id no
                'DataSource' => "I",
                'ExtDocTotal' => 0,

            ];
            $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetails));
            $newDoc->save();

            $documentdocument_lines = [];
            $doctTotal = 0;

            if (count($defaulted_data['document_lines']) <= 0) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Items Required");
            }
            foreach ($defaulted_data['document_lines'] as $key => $value) {
                $LineNum = ++$key;
                $ItemCode = null;
                $Dscription = $value['Dscription'];
                $StockPrice = 0;

                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                if (!$product) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Items Required");
                }

                $ItemCode = $product->ItemCode;
                if ($user->oudg->SellFromBin) {
                    //defaulting item dimensions

                    $dimensions =     (new PriceCalculationController())->getItemDefaultDimensions($product->id);
                }

                if (!$ItemCode) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Items Required");
                }
                $Dscription = $product->ItemName;

                $inventoryDetails = OITW::where('ItemCode', $ItemCode)
                    ->where('WhsCode', $value['FromWhsCod'])
                    ->first();

                $AvgPrice = $inventoryDetails ? $inventoryDetails->AvgPrice : 0;

                if ($product->ManSerNum == "Y" && $request['ObjType'] != 66) {
                    if ($request['ObjType'] == 67) {
                        if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                            return (new ApiResponseService())
                                ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                        }
                    }
                }
                $checkStockAvailabilty = true;
                if ($checkStockAvailabilty) {

                    if ($product->InvntItem == "Y") {

                        $inventoryDetails = OITW::where('ItemCode',  $value['ItemCode'])
                            ->where('WhsCode', $value['FromWhsCod'])
                            ->first();

                        if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                            return (new ApiResponseService())
                                ->apiFailedResponseService(
                                    "Insufficient stock for item: " . $value['Dscription'] . "  " .
                                        "Available Quantity is: " . $inventoryDetails?->OnHand ?? 0
                                );
                        }
                    }
                }
                //Validate Bin-locations
                // if (array_key_exists('bin_allocation', $value) && !empty($value['bin_allocation'])) {
                //     foreach ($value['bin_allocation'] as $key => $BinVal) {
                //         if (!empty($BinVal)) {
                //             $obin = OBIN::where('BinCode', $BinVal['BinCode'])->first();
                //             if (!$obin) {
                //                 return (new ApiResponseService())
                //                     ->apiNotFoundResponse("Bin Code Does Not Exist");
                //             }
                //         }
                //     }
                // }
                // if ($user->oudg->SellFromBin && $request['ObjType'] == 67 && empty($value['bin_allocation'])) {
                //     if (array_key_exists('CogsOcrCo4', $value)) {
                //         $defaults = OUDG::where('CogsOcrCo4', $value['CogsOcrCo4'])->first();
                //         if ($defaults) {
                //             $obin = OBIN::where('id', $defaults->DftBinLoc)->first();

                //             $value['bin_allocation'] =  [
                //                 [
                //                     'BinCode' => $obin->BinCode,
                //                     'QtyVar' => $value['Quantity']
                //                 ]
                //             ];
                //         }
                //     }
                // }



                $doctTotal = $doctTotal + $AvgPrice;
                $rowdetails = [
                    'DocEntry' => $newDoc->id,
                    'OwnerCode' => $request['OwnerCode'], //Owner Code
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $ItemCode, //    Item ID from OITM AUTO INCREMENT
                    'Dscription' => $Dscription, // Item Description
                    'CodeBars' => $value['CodeBars'] ?? null, //    Bar Code
                    'SerialNum' => $value['SerialNum'] ?? null, //    Serial No.
                    'Quantity' => $value['Quantity'] ?? null, //    Quantity
                    'DelivrdQty' => $value['DelivrdQty'] ?? null, //    Delivered Qty

                    'Price' => $AvgPrice, //    Price After Discount
                    'FromWhsCod' => $value['FromWhsCod'],
                    'DiscPrcnt' => 0, //    Discount %
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : null, //    Rate
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                    'PriceAfVAT' => $AvgPrice, //       Gross Price after Discount
                    'PriceBefDi' => $AvgPrice, // Unit Price
                    'LineTotal' => $value['LineTotal'] ?? $value['Price'], //    Total (LC)
                    'WhsCode' => $defaulted_data['ToWhsCode'] ?? null, //    Warehouse Code
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                    'SlpCode' => $defaulted_data['SlpCode'], //    Sales Employee
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                    'OcrCode' => isset($value['OcrCode']) ? $value['OcrCode'] : null, //    Dimension 1
                    'OcrCode2' => isset($value['OcrCode2']) ? $value['OcrCode2'] : null, //    Dimension 2
                    'OcrCode3' => isset($value['OcrCode3']) ? $value['OcrCode3'] : null, //    Dimension 3
                    'OcrCode4' => isset($value['OcrCode4']) ? $value['OcrCode4'] : null, //    Dimension 4
                    'OcrCode5' => isset($value['OcrCode5']) ? $value['OcrCode5'] : null, //    Dimension 5
                    'OpenQty' => array_key_exists('Quantity', $value) ? $value['Quantity'] : null, //    Open Inv. Qty
                    'GrossBuyPr' => array_key_exists('GrossBuyPr', $value) ? $value['GrossBuyPr'] : null, //   Gross Profit Base Price
                    'GPTtlBasPr' => array_key_exists('GPTtlBasPr', $value) ? $value['GPTtlBasPr'] : null, //    Gross Profit Total Base Price

                    'ObjType' => $ObjType ?? null,
                    'BaseType' => array_key_exists('BaseType', $value)  ?  $value['BaseType'] : null, //    Base Type
                    'BaseRef' =>  array_key_exists('BaseRef', $value) ?  $value['BaseRef'] : null, //    Base Ref.
                    'BaseEntry' => array_key_exists('BaseEntry', $value) ?   $value['BaseEntry'] :  null, //    Base Key
                    'BaseLine' => array_key_exists('BaseLine', $value)  ? $value['BaseLine'] : null,  //    Base Row
                    'SpecPrice' => array_key_exists('SpecPrice', $value) ? $value['SpecPrice'] : null, //    Price Source
                    'VatSum' => array_key_exists('VatSum', $value) ? $value['VatSum'] : null, //    Tax Amount (LC)
                    'GrssProfit' => array_key_exists('GrssProfit', $value) ? $value['GrssProfit'] : null, //    Gross Profit (LC)
                    'PoTrgNum' => array_key_exists('PoTrgNum', $value) ? $value['PoTrgNum'] : null, //    Procurement Doc.
                    'OrigItem' => array_key_exists('OrigItem', $value) ? $value['OrigItem'] : null, //    Original Item
                    'BackOrdr' => array_key_exists('BackOrdr', $value) ? $value['BackOrdr'] : null, //    Partial Delivery
                    'FreeTxt' => array_key_exists('FreeTxt', $value) ? $value['FreeTxt'] : null, //    Free Text
                    'TrnsCode' => array_key_exists('TrnsCode', $value) ? $value['TrnsCode'] : null, //    Shipping Type
                    'UomCode' => $value['UomCode'] ?? null, //    UoM Code
                    'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null, //    UoM Name
                    'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                    'Text' => array_key_exists('Text', $value) ? $value['Text'] : null, //    Item Details

                    'GTotal' => array_key_exists('GTotal', $value) ? $value['GTotal'] : null, //    Gross Total
                    'AgrNo' => array_key_exists('AgrNo', $value) ? $value['AgrNo'] : null, //    Blanket Agreement No.
                    'LinePoPrss' => array_key_exists('LinePoPrss', $value) ? $value['LinePoPrss'] : null, //    Allow Procmnt. Doc.

                    //Cogs Values
                    'CogsOcrCod' => isset($value['OcrCode']) ? $value['OcrCode'] :  null,
                    'CogsOcrCo2' => isset($value['OcrCode2']) ? $value['OcrCode2'] :  null,
                    'CogsOcrCo3' => isset($value['OcrCode3']) ? $value['OcrCode3'] : null,
                    'CogsOcrCo4' => isset($value['OcrCode4']) ? $value['OcrCode4'] : null,
                    'CogsOcrCo5' => isset($value['OcrCode5']) ? $value['OcrCode5'] : null,
                    //Inventory Transaction  Value
                    'PQTReqDate' => $defaulted_data['ReqDate'] ?? null,

                    'BPLId' => $defaulted_data['BPLId'],
                    'U_StockWhse' => isset($value['U_StockWhse']) ? $value['U_StockWhse'] : null,
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                    'StockPrice' => $StockPrice,
                    'U_Promotion' => isset($value['U_Promotion']) ? $value['U_Promotion'] : null,

                ];

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

                    $FromBinCod =    $value['FromBinCod'] ?? null;

                    foreach ($value['bin_allocation'] as $key => $BinVal) {
                    //     if (!empty($BinVal)) {
                    //         $SubLineNum = ++$key;
                    //         $obin = OBIN::where('BinCode', $BinVal['BinCode'])->first();
                    //         $bindata = $lineModel['ChildTable']::create([
                    //             'DocEntry' => $newDoc->id,
                    //             'BinAllocSe' => $LineNum,
                    //             'LineNum' => $LineNum,
                    //             'SubLineNum' => $SubLineNum,
                    //             'SnBType' => null,
                    //             'SnBMDAbs' => null,
                    //             'BinAbs' =>  $obin->id,
                    //             'Quantity' =>  $BinVal['QtyVar'],
                    //             'ItemCode' => $ItemCode,
                    //             'WhsCode' =>  $defaulted_data['ToWhsCode'] ?? $defaulted_data['WhsCode'],
                    //             'ObjType' =>  $ObjType,
                    //             'AllowNeg' => 'N',
                    //             'BinActTyp' => 1
                    //         ]);

                    //         $resdata =    (new InventoryService())->binAllocations(
                    //             $ObjType,
                    //             $ItemCode,
                    //             $BinVal,
                    //             $value['ToWhsCode'],
                    //             $FromBinCod,
                    //         );
                    //     }
                    // }
                    // if (isset($defaulted_data['ToWhsCode']) && $defaulted_data['ToWhsCode'] !== null) {
                    //     $WhsCode = $defaulted_data['ToWhsCode'];
                    // } elseif (isset($defaulted_data['WhsCode']) && $defaulted_data['WhsCode'] !== null) {
                    //     $WhsCode = $defaulted_data['WhsCode'];
                    // } else {
                    //     $WhsCode = null;
                    // }

                    (new InventoryService())->binQuantities($value, $lineModel, $newDoc->id, $LineNum, $ItemCode,
                     $WhsCode, $ObjType, $FromBinCod,$newDoc);
                }

                /**
                 * Saving Serial Numbers
                 */

                if ($defaulted_data['DocType'] == "I" && $product->ManSerNum == "Y") {
                    $saveSerialDetails = false;
                    if ($ObjType == 67) {
                        $saveSerialDetails = true;
                    }

                    if ($saveSerialDetails) {
                        foreach ($value['SerialNumbers'] as $key => $serial) {
                            $LineNum = $key;
                            SRI1::create([
                                "ItemCode" => $ItemCode,
                                "SysSerial" => $serial['SysNumber'] ?? $serial['SysSerial'],
                                "LineNum" => $rowItems->id,
                                "BaseType" => $saveToDraft ? 112 : $ObjType,
                                "BaseEntry" => $newDoc->id,
                                "CardCode" => null,
                                "CardName" => null,
                                "WhsCode" => $value['FromWhsCod'],
                                "ItemName" => $Dscription,
                            ]);
                        }
                    }
                }

                array_push($documentdocument_lines, $rowItems);
            }

            $newDoc->update([
                'DocTotal' => $doctTotal,
            ]);
            //Stored Procedure Validations

            $objectTypePassedToTns = $request['ObjType'];

            if ($TargetTables->ObjectID == 112) {
                $objectTypePassedToTns = 112;
            }

            // $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($objectTypePassedToTns, "A", $newDoc->id);
            // if ($storedProcedureResponse) {
            //     if ($storedProcedureResponse->error != 0) {
            //         return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
            //     }
            // }

            //Validating Draft using Oringal base type
            if ($objectTypePassedToTns == 112) {
                $mockedDataDraftMessage = (new GeneralDocumentValidationService())->draftValidation($newDoc, $documentdocument_lines);
                if ($mockedDataDraftMessage) {
                    return (new ApiResponseService())->apiFailedResponseService($mockedDataDraftMessage);
                }
            }
            if ($objectTypePassedToTns != 112) {
                NumberingSeries::dispatch($defaulted_data['Series']);
            }

            $newDoc->newObjType = $objectTypePassedToTns;
            DB::connection("tenant")->commit();
            $documentForDirecPostingToSAP = (new DocumentsService())->getDocumentForDirectPostingToSAP($newDoc->ObjType, $newDoc->id);
            $newDoc->documentForDirecPostingToSAP = $documentForDirecPostingToSAP;

            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
            Log::info($th);
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $isDoc = request()->filled('isDoc') ? request()->input('isDoc') : 1;
        $DocEntry = $id;
        $ObjType = request()->filled('ObjType') ? request()->input('ObjType') : 66;

        $originalObjType = $ObjType;
        if ($isDoc == 0) {
            $ObjType = 112;
        }

        // 'document_lines.oitm.itm1',
        // 'document_lines.oitm.oitw',
        // 'document_lines.oitm.inventoryuom',
        // 'document_lines.oitm.ougp.ouom',
        // 'document_lines.oitm.oitb',
        // 'document_lines.taxgroup'

        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'view');

        $data = $DocumentTables->ObjectHeaderTable::with(
            'objecttype',
            'department',
            'document_lines.oitm',
            'branch',
            'CreatedBy',
            'location'
        )
            ->where('id', $DocEntry)
            ->first();

        $document_lines = $data->document_lines;

        foreach ($document_lines as $key => $row) {
            $row->formattedLineTotal = number_format($row->LineTotal, 2);
            $row->formattedPrice = number_format($row->Price, 2);
            $row->formattedPriceBefDisc = number_format($row->PriceBefDi, 2);
        }

        $oats = OATS::where('DocEntry', $DocEntry)
            ->where('ObjType', $ObjType)->get();
        foreach ($oats as $key => $file) {
            $file->realPath = asset($file->Path);
        }
        $data->oats = $oats;
        $data->isDoc = $isDoc;

        if ($isDoc == 0) {
            $data->DocStatus = "Draft";
        }
        $generalSettings = OADM::where('id', 1)->value('printUnsyncDocs');
        if ($generalSettings == 1 && $data->ExtRef == null) {
            $data->printUnsyncDocs = 1;
        }

        // return $data->draftKey;

        /**
         * Addin Approvers
         */

        $data->originIsApproval = 0;
        $owdd = OWDD::where('DocEntry', $data->ExtRef)->first();

        if ($owdd) {
            $data->originIsApproval = 1;
            $approvers = WDD1::where('WddCode', $owdd->WddCode)->where('Status', "Y")->get();

            foreach ($approvers as $key => $approver) {
                $userDetails = User::where('ExtRef', $approver->UserID)->first();
                $approver->userDetails = $userDetails;

                if ($userDetails) {
                    $approver->Date = now()->format('Y-m-d');
                    $approver->imagePath = $userDetails ? "data:image/jpeg;base64," . $userDetails->signaturePath : null;
                }
            }
            $data->approvers = $approvers;
        }

        /**
         * Format Values for Reports;
         */
        $data->formattedSubDocTotal = number_format($data->DocTotal - $data->VatSum, 2);
        $data->formattedDocTotalBeforeTax = number_format($data->DocTotal - $data->VatSum, 2);
        $data->formattedVatSum = number_format($data->VatSum, 2);
        $data->formattedDocTotal = number_format($data->DocTotal, 2);
        $data->formattedBalance = number_format($data->DocTotal - $data->PaidToDate, 2);
        $data->formattedPaidToDate = number_format($data->PaidToDate, 2);

        return $data;
    }

    public function getSingleDocData($ObjType, $DocEntry)
    {

        $isDoc = request()->filled('isDoc') ? request()->input('isDoc') : 1;


        $originalObjType = $ObjType;
        if ($isDoc == 0) {
            $ObjType = 112;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$DocumentTables) {
            return (new ApiResponseService())
                ->apiNotFoundResponse("Not found document with objtype " . $ObjType);
        }


        //    (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'view');

        try {
            $data = $DocumentTables->ObjectHeaderTable::with(
                'objecttype',
                'department',
                'document_lines.oitm',
                'branch',
                'CreatedBy:id,name',
                'location',
                'bin_allocations'
            )
                ->where('id', $DocEntry)
                ->first();
            if (!$data) {
                return (new ApiResponseService())->apiNotFoundResponse('No data');
            }
            // bin_allocations
            // $result = array_filter($DocumentTables->pdi1->toArray(), function ($item) {
            //     return $item['ChildType'] === 'bin_allocations';
            // });
            // $firstValue = collect($result)->first();

            // foreach ($data->document_lines as $d) {
            //     $line = $firstValue['ChildTable']::where('LineNum', $d->LineNum)->where('DocEntry', $data->id)->get();
            //     $d->bin_alloc = $line;
            // }


            $document_lines = $data->document_lines;

            foreach ($document_lines as $key => $row) {
                $serialNumbers = SRI1::where('BaseType', $ObjType)
                    ->where('BaseEntry', $data->id)
                    ->where('LineNum', $row->id)
                    ->get();

                // return $serialNumbers;
                foreach ($serialNumbers as $key => $serial) {
                    $serial->osrn = OSRN::where('SysNumber', $serial->SysSerial)
                        ->where('ItemCode', $row->ItemCode)
                        ->first();
                }
                $row->SerialNumbers = $serialNumbers;
                $row->formattedLineTotal = number_format($row->LineTotal, 2);
                $row->formattedPrice = number_format($row->Price, 2);
                $row->formattedPriceBefDisc = number_format($row->PriceBefDi, 2);
            }

            $oats = OATS::where('DocEntry', $DocEntry)
                ->where('ObjType', $ObjType)->get();
            foreach ($oats as $key => $file) {
                $file->realPath = asset($file->Path);
            }
            $data->oats = $oats;
            $data->isDoc = $isDoc;

            if ($isDoc == 0) {
                $data->DocStatus = "Draft";
            }
            $generalSettings = OADM::where('id', 1)->value('printUnsyncDocs');
            if ($generalSettings == 1 && $data->ExtRef == null) {
                $data->printUnsyncDocs = 1;
            }

            // return $data->draftKey;

            /**
             * Addin Approvers
             */

            $data->originIsApproval = 0;
            $owdd = OWDD::where('DocEntry', $data->ExtRef)->first();

            if ($owdd) {
                $data->originIsApproval = 1;
                $approvers = WDD1::where('WddCode', $owdd->WddCode)->where('Status', "Y")->get();

                foreach ($approvers as $key => $approver) {
                    $userDetails = User::where('ExtRef', $approver->UserID)->first();
                    $approver->userDetails = $userDetails;

                    if ($userDetails) {
                        $approver->Date = now()->format('Y-m-d');
                        $approver->imagePath = $userDetails ? "data:image/jpeg;base64," . $userDetails->signaturePath : null;
                    }
                }
                $data->approvers = $approvers;
            }

            // $BINALLOC = DB::connection('tenant')->table('o_w_t_r_s AS T0')
            //     ->join('w_t_r1_s AS T1', 'T0.id', '=', 'T1.DocEntry')
            //     ->join('o_w_h_s_s AS T3', 'T1.WhsCode', '=', 'T3.WhsCode')
            //     ->join('o_w_h_s_s AS T4', 'T1.FromWhsCod', '=', 'T4.WhsCode')
            //     ->leftjoin('o_s_l_p_s AS T5', 'T0.SlpCode', '=', 'T5.SlpCode')
            //     ->join('o_i_l_m_s AS T6', 'T1.ItemCode', '=', 'T6.ItemCode')
            //     ->leftJoin('o_c_r_d_s AS T2', 'T0.CardCode', '=', 'T2.CardCode')
            //     ->join('o_i_l_m_s AS T7', function ($join) {
            //         $join->on('T7.DocEntry', '=', 'T1.DocEntry')
            //             ->on('T7.TransType', '=', 'T1.ObjType')
            //             ->on('T7.DocLineNum', '=', 'T1.LineNum');
            //     })
            //     ->Join('o_b_t_l_s AS T8', 'T8.MessageID', '=', 'T7.id')
            //     ->Join('o_b_i_n AS T10', 'T8.BinAbs', '=', 'T10.id')
            //     ->where('T0.id', $DocEntry)
            //     ->where('T6.DocEntry', $DocEntry)
            //     ->select(
            //         'T0.id',
            //         'T0.ObjType',
            //         'T0.BPLId',
            //         'T0.ToWhsCode',
            //         'T0.SlpCode',
            //         'T1.WhsCode',
            //         'T1.LineNum',
            //         'T3.WhsName',
            //         'T6.ItemCode',
            //         'T6.Quantity',
            //         'T7.DocLineNum',
            //         'T7.TransType',
            //         'T7.DocEntry',
            //         'T1.DocEntry As T1DocEntry',
            //         'T8.id as ObtlID',
            //         'T8.MessageID',
            //         'T10.BinCode',
            //         'T8.BinAbs',
            //     )
            //     ->get();
            // return $BINALLOC;
            /**
             * Format Values for Reports;
             */
            $data->formattedSubDocTotal = number_format($data->DocTotal - $data->VatSum, 2);
            $data->formattedDocTotalBeforeTax = number_format($data->DocTotal - $data->VatSum, 2);
            $data->formattedVatSum = number_format($data->VatSum, 2);
            $data->formattedDocTotal = number_format($data->DocTotal, 2);
            $data->formattedBalance = number_format($data->DocTotal - $data->PaidToDate, 2);
            $data->formattedPaidToDate = number_format($data->PaidToDate, 2);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
