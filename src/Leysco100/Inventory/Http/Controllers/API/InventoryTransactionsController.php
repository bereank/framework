<?php



namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\MarketingDocuments\Models\OATS;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\MarketingDocuments\Services\DatabaseValidationServices;
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

        DB::connection("tenant")->beginTransaction();
        try {

            /**
             * Handling  Document Numbering
             */
            if ($request['DocNum'] && $request['Series']) {
                $DocNum = (new DocumentsService())
                    ->documentNumberingService(
                        $request['DocNum'],
                        $request['Series']
                    );
                $doc_number = $DocNum;
                $Series =    $request['Series'];
            } else {
                $DocNum  = (new DocumentsService())
                    ->getNumSerieByObjectId($request['ObjType']);
                $doc_number =  $DocNum['NextNumber'];
                $Series =  $DocNum['id'];
            }


            $NewDocDetails = [
                'ObjType' => $request['ObjType'],
                'DocType' => $request['DocType'],
                'DocNum' => $doc_number ?? null,
                'Series' => $Series ?? null,
                //                'ToWhsCode' => $request['ToWhsCode'],
                'Filler' => $request['FromWhsCod'],

                'SlpCode' => $request['SlpCode'], // Sales Employee
                'U_SalePipe' => $request['U_SalePipe'], // Sales Pipe Line
                'OwnerCode' => $user->EmpID, //Owner Code
                'U_CashName' => $request['U_CashName'], //Cash Customer  Name
                'U_CashNo' => $request['U_CashNo'], // Cash Customer No
                'U_IDNo' => $request['U_IDNo'], // Id no
                'NumAtCard' => $request['NumAtCard'] ? $request['NumAtCard'] : null,
                'CurSource' => $request['CurSource'],
                'DocTotal' => $request['DocTotal'],
                'VatSum' => $request['VatSum'] ?? 0,
                'DocDate' => $request['DocDate'], //PostingDate
                'TaxDate' => $request['TaxDate'] ?? now(), //Document Date
                'DocDueDate' => $request['DocDueDate'] ?? now(), // Delivery Date
                'ReqDate' => $request['DocDueDate'],
                'CntctCode' => $request['CntctCode'], //Contact Person
                'AgrNo' => $request['AgrNo'],
                'LicTradNum' => $request['LicTradNum'],
                'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //BaseKey
                'BaseType' => $request['BaseType'] ? $request['BaseType'] : null, //BaseKey
                'UserSign' => $user->id,
                //Inventory Transaction Values
                'Ref2' => $request['Ref2'] ? $request['Ref2'] : null, // Ref2
                'GroupNum' => $request['GroupNum'] ? $request['GroupNum'] : null, //[Price List]
                'ToWhsCode' => $request['ToWhsCode'] ? $request['ToWhsCode'] : null, //To Warehouse Code
                //SeriesDocument
                'DiscPrcnt' => $request['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' => $request['DiscSum'], // Discount Sum
                'BPLId' => $request['BPLId'],
                'U_SaleType' => $request['U_SaleType'], // Sale Type
                'Comments' => $request['Comments'], //comments
                'NumAtCard2' => $request['NumAtCard2'],
                'JrnlMemo' => $request['JrnlMemo'], // Journal Remarks
                'UseShpdGd' => $request['UseShpdGd'] ?? "N",
                'U_ServiceCall' => $request['U_ServiceCall'],
                'U_DemoLocation' => $request['U_DemoLocation'],
                'U_Technician' => $request['U_Technician'],
                'U_Location' => $request['U_Location'],
                'U_MpesaRefNo' => $request['U_MpesaRefNo'],
                'U_PCash' => $request['U_PCash'],
                'U_transferType' => $request['U_transferType'],
                'U_SSerialNo' => $request['U_SSerialNo'],
                'U_TypePur' => $request['U_TypePur'],
                'U_NegativeMargin' => $request['U_NegativeMargin'],
                'U_BaseDoc' => $request['U_BaseDoc'],

                'DataSource' => "I",
                'ExtDocTotal' => 0,

            ];
            $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetails));
            $newDoc->save();

            $documentdocument_lines = [];
            $doctTotal = 0;

            if (count($request['document_lines']) <= 0) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Items Required");
            }
            foreach ($request['document_lines'] as $key => $value) {
                $LineNum = $key;
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

                $doctTotal = $doctTotal + $AvgPrice;
                $rowdetails = [
                    'DocEntry' => $newDoc->id,
                    'OwnerCode' => $request['OwnerCode'], //Owner Code
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $ItemCode, //    Item ID from OITM AUTO INCREMENT
                    'Dscription' => $Dscription, // Item Description
                    'CodeBars' => $value['CodeBars']??null, //    Bar Code
                    'SerialNum' => $value['SerialNum'] ??null, //    Serial No.
                    'Quantity' => $value['Quantity']??null, //    Quantity
                    'DelivrdQty' => $value['DelivrdQty']??null,//    Delivered Qty

                    'Price' => $AvgPrice, //    Price After Discount
                    'FromWhsCod' => $value['FromWhsCod'],
                    'DiscPrcnt' => 0, //    Discount %
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : null, //    Rate
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                    'PriceAfVAT' => $AvgPrice, //       Gross Price after Discount
                    'PriceBefDi' => $AvgPrice, // Unit Price
                    'LineTotal' => $value['LineTotal'] ?? $value['Price'], //    Total (LC)
                    'WhsCode' => $request['ToWhsCode'] ?? null, //    Warehouse Code
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                    'SlpCode' => $request['SlpCode'], //    Sales Employee
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                    'OcrCode' => $value['OcrCode']??null, //    Dimension 1
                    'OcrCode2' => $value['OcrCode2']??null, //    Dimension 2
                    'OcrCode3' => $value['OcrCode3']??null, //    Dimension 3
                    'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                    'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                    'OpenQty' => array_key_exists('Quantity', $value) ? $value['Quantity'] : null, //    Open Inv. Qty
                    'GrossBuyPr' => array_key_exists('GrossBuyPr', $value) ? $value['GrossBuyPr'] : null, //   Gross Profit Base Price
                    'GPTtlBasPr' => array_key_exists('GPTtlBasPr', $value) ? $value['GPTtlBasPr'] : null, //    Gross Profit Total Base Price

                    'BaseType' => $request['BaseType'] ?? $request['BaseType'], //    Base Type
                    'BaseRef' => $request['BaseRef'] ? $request['BaseRef'] : null, //    Base Ref.
                    'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //    Base Key
                    'BaseLine' => $value['LineNum'], //    Base Row
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
                    'CogsOcrCod' => $value['OcrCode']?? null,
                    'CogsOcrCo2' => $value['OcrCode2']?? null,
                    'CogsOcrCo3' => $value['OcrCode3']?? null,
                    'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                    'CogsOcrCo5' => $value['OcrCode5'] ?? null,
                    //Inventory Transaction  Value
                    'PQTReqDate' => $request['ReqDate']?? null,

                    'BPLId' => $request['BPLId'],
                    'U_StockWhse' => isset($value['U_StockWhse']) ? $value['U_StockWhse'] : null,
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                    'StockPrice' => $StockPrice,
                    'U_Promotion' => isset($value['U_Promotion']) ? $value['U_Promotion'] : null,

                ];

                $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
                $rowItems->save();

                /**
                 * Saving Serial Numbers
                 */

                if ($request['DocType'] == "I" && $product->ManSerNum == "Y") {
                    $saveSerialDetails = false;
                    if ($request['ObjType'] == 67) {
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
                NumberingSeries::dispatch($Series);
            }

            $newDoc->newObjType = $objectTypePassedToTns;
            DB::connection("tenant")->commit();
            $documentForDirecPostingToSAP = (new DocumentsService())->getDocumentForDirectPostingToSAP($newDoc->ObjType, $newDoc->id);
            $newDoc->documentForDirecPostingToSAP = $documentForDirecPostingToSAP;

            //            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
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
        $isDoc = \Request::get('isDoc');
        $DocEntry = $id;
        $ObjType = 66;

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

        $data = $DocumentTables->ObjectHeaderTable::with('objecttype', 'department', 'document_lines.oitm', 'branch', 'CreatedBy', 'location')
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
        $isDoc = \Request::get('isDoc');
        $DocEntry = $DocEntry;
        $ObjType = $ObjType;

        $originalObjType = $ObjType;
        if ($isDoc == 0) {
            $ObjType = 112;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'view');

        $data = $DocumentTables->ObjectHeaderTable::with('objecttype', 'department', 'document_lines.oitm', 'branch', 'CreatedBy', 'location')
            ->where('id', $DocEntry)
            ->first();

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
}
