<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Leysco100\Shared\Models\Administration\Models\NNM1;

/**
 * Service for Marke
 */
class DocumentsService
{
    /**
     * Calculating Numbering service
     */

    public function documentNumberingService($newDocNum, $series)
    {
        $nnm1 = NNM1::where('id', $series)->first();

        /**
         * If Manual Use the Number inserted by the user
         */
        if ($nnm1->IsManual == "Y") {
            return $newDocNum;
        }
        /**
         * Else if not Manual Calculate new Numbering Series and return it to user
         */
        $DocNum = sprintf("%0" . $nnm1->NumSize . "s", $nnm1->NextNumber);
        return $DocNum;
    }

    /**
     * Checking Current User Approval
     */
    public function checkApproval($request)
    {

        /**
         * Check if the current user has Approval Process
         * For the documement creating
         */
        $fetchAllAprovals = OWTM::with('wtm1', 'wtm2', 'wtm3')
            ->whereHas('wtm1', function ($q) {
                $q->where('UserID', Auth::user()->id);
            })
            ->whereHas('wtm3', function ($q) use ($request) {
                $q->where('TransType', $request['ObjType']);
            })
            ->orderBy('id', 'asc')
            ->get();

        if ($fetchAllAprovals->isNotEmpty()) {
            DB::beginTransaction();
            try {
                $draftDetails = $this->creatingDraftDocument($request, $fetchAllAprovals);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                abort_if(
                    $th,
                    (new ApiResponseService())->apiFailedResponseService($th->getMessage())
                );
            }
            abort_if(
                $fetchAllAprovals->isNotEmpty(),
                ((new ApiResponseService())->apiSuccessDraftCreationResponseService($draftDetails))
            );
        }
    }

    public function creatingDraftDocument($request, $fetchAllAprovals)
    {
        $user = Auth::user();
        $ORDFDetails = [
            'ObjType' => $request['ObjType'],
            'CardCode' => $request['CardCode'] ? $request['CardCode'] : null,
            'CardName' => $request['CardName'] ? $request['CardName'] : null,
            'SlpCode' => OUDG::where('id', $user->DfltsGroup)->value('SalePerson'), // Sales Employee
            'NumAtCard' => $request['NumAtCard'] ? $request['NumAtCard'] : null,
            'CurSource' => $request['CurSource'],
            'DocTotal' => $request['DocTotal'],
            'VatSum' => $request['VatSum'],
            'DocDate' => $request['DocDate'], //PostingDate
            'TaxDate' => $request['TaxDate'], //Document Date
            'DocDueDate' => $request['DocDueDate'], // Delivery Date
            'CntctCode' => $request['CntctCode'], //Contact Person
            'AgrNo' => $request['AgrNo'],
            'LicTradNum' => $request['LicTradNum'],
            'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //BaseKey
            'BaseType' => $request['BaseType'] ? $request['BaseType'] : -1, //BaseKey
            'UserSign' => $user->id,
            //Inventory Transaction Values
            'Ref2' => $request['Ref2'] ? $request['Ref2'] : null, // Ref2
            'GroupNum' => $request['GroupNum'] ? $request['GroupNum'] : null, //[Price List]
            'ToWhsCode' => $request['ToWhsCode'] ? $request['ToWhsCode'] : null, //To Warehouse Code
            //SeriesDocument

            'DiscPrcnt' => $request['DiscPrcnt'], //Discount Percentages
            'DiscSum' => $request['DiscSum'], // Discount Sum
            'UserSign' => $user->id,
        ];
        $newORDF = new ODRF($ORDFDetails);
        $newORDF->save();

        foreach ($request['document_lines'] as $key => $value) {
            $rowdetails = [
                'DocEntry' => $newORDF->id,
                'LineNum' => $key + 1, //    Row Number
                'ItemCode' => array_key_exists('ItemCode', $value) ? $value['ItemCode'] : null, //    Item No.
                'Dscription' => $value['Dscription'], // Item Description
                'CodeBars' => $value['CodeBars'], //    Bar Code
                'SerialNum' => $value['SerialNum'], //    Serial No.
                'Quantity' => $value['Quantity'], //    Quantity
                'DelivrdQty' => $value['DelivrdQty'], //    Delivered Qty
                'InvQty' => $value['InvQty'], //   Qty(Inventory UoM)
                'OpenInvQty' => $value['OpenInvQty'], //Open Inv. Qty ------
                'PackQty' => $value['PackQty'], //    No. of Packages
                'Price' => $value['Price'], //    Price After Discount
                'DiscPrcnt' => array_key_exists('DiscPrcnt', $value) ? $value['DiscPrcnt'] : null, //    Discount %
                'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : null, //    Rate
                'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                'PriceAfVAT' => array_key_exists('PriceAfVAT', $value) ? $value['PriceAfVAT'] : null, //       Gross Price after Discount
                'PriceBefDi' => array_key_exists('PriceBefDi', $value) ? $value['PriceBefDi'] : null, // Unit Price
                'LineTotal' => array_key_exists('LineTotal', $value) ? $value['LineTotal'] : null, //    Total (LC)
                'WhsCode' => array_key_exists('WhsCode', $value) ? $value['WhsCode'] : null, //    Warehouse Code
                'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                'SlpCode' => array_key_exists('SlpCode', $value) ? $value['SlpCode'] : null, //    Sales Employee
                'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                'OcrCode' => array_key_exists('OcrCode', $value) ? $value['OcrCode'] : null, //    Dimension 1
                'OcrCode2' => array_key_exists('OcrCode2', $value) ? $value['OcrCode2'] : null, //    Dimension 2
                'OcrCode3' => array_key_exists('OcrCode3', $value) ? $value['OcrCode3'] : null, //    Dimension 3
                'OcrCode4' => array_key_exists('OcrCode4', $value) ? $value['OcrCode4'] : null, //    Dimension 4
                'OcrCode5' => array_key_exists('OcrCode5', $value) ? $value['OcrCode5'] : null, //    Dimension 5
                'OpenQty' => array_key_exists('Quantity', $value) ? $value['Quantity'] : null, //    Open Inv. Qty
                'GrossBuyPr' => array_key_exists('GrossBuyPr', $value) ? $value['GrossBuyPr'] : null, //   Gross Profit Base Price
                'GPTtlBasPr' => array_key_exists('GPTtlBasPr', $value) ? $value['GPTtlBasPr'] : null, //    Gross Profit Total Base Price
                'TreeType' => array_key_exists('TreeType', $value) ? $value['TreeType'] : null, //    BOM Type
                'BaseType' => $request['BaseType'] ? $request['BaseType'] : -1, //    Base Type
                'BaseRef' => $request['BaseRef'] ? $request['BaseRef'] : null, //    Base Ref.
                'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //    Base Key
                'BaseLine' => array_key_exists('BaseLine', $value) ? $value['BaseLine'] : null, //    Base Row
                'SpecPrice' => array_key_exists('SpecPrice', $value) ? $value['SpecPrice'] : null, //    Price Source
                'VatSum' => array_key_exists('VatSum', $value) ? $value['VatSum'] : null, //    Tax Amount (LC)
                'GrssProfit' => array_key_exists('GrssProfit', $value) ? $value['GrssProfit'] : null, //    Gross Profit (LC)
                'PoTrgNum' => array_key_exists('PoTrgNum', $value) ? $value['PoTrgNum'] : null, //    Procurement Doc.
                'OrigItem' => array_key_exists('OrigItem', $value) ? $value['OrigItem'] : null, //    Original Item
                'BackOrdr' => array_key_exists('BackOrdr', $value) ? $value['BackOrdr'] : null, //    Partial Delivery
                'FreeTxt' => array_key_exists('FreeTxt', $value) ? $value['FreeTxt'] : null, //    Free Text
                'TrnsCode' => array_key_exists('TrnsCode', $value) ? $value['TrnsCode'] : null, //    Shipping Type
                'UomCode' => array_key_exists('UomCode', $value) ? $value['UomCode'] : null, //    UoM Code
                'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null, //    UoM Name
                'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                'Text' => array_key_exists('Text', $value) ? $value['Text'] : null, //    Item Details
                'OwnerCode' => array_key_exists('OwnerCode', $value) ? $value['OwnerCode'] : null, //    Owner
                'GTotal' => array_key_exists('GTotal', $value) ? $value['GTotal'] : null, //    Gross Total
                'AgrNo' => array_key_exists('AgrNo', $value) ? $value['AgrNo'] : null, //    Blanket Agreement No.
                'LinePoPrss' => array_key_exists('LinePoPrss', $value) ? $value['LinePoPrss'] : null, //    Allow Procmnt. Doc.

                //Inventory Transaction  Value
                'FromWhsCod' => array_key_exists('FromWhsCod', $value) ? $value['FromWhsCod'] : null, // // From Warehouse Code
            ];
            $drf1 = new DRF1($rowdetails);
            $drf1->save();
        }

        //Creating Message Alert
        $messageAlerts = OALR::create([
            'UserSign' => Auth::user()->id,
            'DraftEntry' => $newORDF->id,
            'Subject' => "Request for Approving Document Generation",
        ]);

        foreach ($fetchAllAprovals as $key => $val) {

            //Creating OWDD
            $creatingOWDD = OWDD::create([
                'WtmCode' => $val->id,
                'OwnerID' => Auth::user()->id,
                'DocEntry' => 1,
                'ObjType' => $request['ObjType'],
                'DocDate' => $request['DocDate'],
                'CurrStep' => 1,
                'Status' => "W",
                'Remarks' => 1,
                'UserSign' => 1,
                'IsDraft' => "Yes",
                'MaxReqr' => 1,
                'MaxRejReqr' => 1,
                'SrcDocEnt' => 1,
                'DraftType' => 1,
                'DraftEntry' => $newORDF->id,
                'BFType' => 1,
            ]);

            //Get All Stages For each Approval Template
            $wtm2 = WTM2::where('WtmCode', $val->id)->get();

            foreach ($wtm2 as $key => $wwt2) {
                $wst1 = WST1::where('WstCode', $wwt2->WstCode)->get();
                foreach ($wst1 as $key => $wst) {
                    if ($key == 0) {
                        //Inbox all Approvers
                        $inbox = OAIB::create([
                            'AlertCode' => $messageAlerts->id,
                            'RecDate' => Carbon::now(),
                            'UserSign' => $wst->UserID,
                        ]);
                    }
                    $creatingOWDD = WDD1::create([
                        'WddCode' => $creatingOWDD->id,
                        'StepCode' => $wwt2->id,
                        'UserID' => $wst->UserID,
                        'Status' => 'W',
                        'UserSign' => Auth::user()->id,
                        'CreateDate' => now(),
                        'SortId' => $key + 1,
                    ]);
                }
            }

            //Creating ODDQ1
        }

        //Send Request to Inbox of every user;

        return ODRF::with(
            'outlet',
            'drf1.oitm.itm1',
            'drf1.oitm.inventoryuom',
            'drf1.oitm.ougp.ouom',
            'drf1.oitm.oitb'
        )
            ->where('id', $newORDF->id)
            ->first();
    }

    public function gettingNumberingSeries($ObjectCode)
    {
        //default Numembering Seires
        $documentDefaultSeries = NNM1::where('id', ONNM::where('ObjectCode', $ObjectCode)
            ->value('DfltSeries'))
            ->where('Locked', 'N')
            ->first();

        $currentUserDefaultSeries = NNM2::with('nnm1')->where('ObjectCode', $ObjectCode)
            ->whereHas('nnm1', function ($q) {
                $q->where('Locked', 'N');
            })
            ->where('UserSign', Auth::user()->id)
            ->first();

        //
        if ($currentUserDefaultSeries) {
            $nnm1Data = NNM1::where('id', $currentUserDefaultSeries['Series'])
                ->where('Locked', 'N')
                ->first();
            $nnm1Data->NextNumber = $nnm1Data->BeginStr . sprintf("%0" . $nnm1Data->NumSize . "s", $nnm1Data->NextNumber) . $nnm1Data->EndStr;

            $documentDefaultSeries = $nnm1Data;
        }

        $DocNum = $documentDefaultSeries->BeginStr . sprintf("%0" . $documentDefaultSeries->NumSize . "s", $documentDefaultSeries->NextNumber) . $documentDefaultSeries->EndStr;

        $details = [
            'DocNum' => $DocNum,
            'Series' => $documentDefaultSeries->id,
        ];

        return $details;
    }
    public function getNumSerieByObjectId($ObjectCode)
    {
        $form = APDI::with('pdi1')
            ->where('ObjectID', $ObjectCode)
            ->first();

        $currentUserDefaultSeries = NNM2::with('nnm1')->where('ObjectCode', $form->id)
            ->whereHas('nnm1', function ($q) {
                $q->where('Locked', 'N');
            })
            ->where('UserSign', Auth::user()->id)
            ->first();

        if ($currentUserDefaultSeries) {
            $nnm1Data = NNM1::where('id', $currentUserDefaultSeries['Series'])
                ->where('Locked', 'N')
                ->first();
            $nnm1Data->NextNumber = sprintf("%0" . $nnm1Data->NumSize . "s", $nnm1Data->NextNumber);

            $documentDefaultSeries = $nnm1Data;
        }
        return $documentDefaultSeries;
    }
    public function getCardName(int $CardID)
    {
        return OCRD::where('id', $CardID)->first();
    }

    public function sendingSMS($DocEntry)
    {
        $data = OADM::where('id', 1)->first();

        if (!$data->NotifAlert) {
            info("SMS NOT ENABLE");
            return;
        }

        $document = ORDR::where('id', $DocEntry)->first();
        $customerData = OCRD::where('CardCode', $document->CardCode)->first();
        $orderSummary = "Order No: " . $document->ExtRefDocNum . ",\n" . "Order Amount: " . number_format($document->ExtDocTotal, 2) . ". Thanks for choosing us.\nDate : " . $document->DocDate . ",\n";
        $messageSignature = "SAMWEST DISTRIBUTORS";

        $message = "Hi " . $document->CardName . ",\n" . "Your order has been received" . ",\n" . $orderSummary . "\n\n" . $messageSignature;

        if ($customerData->Phone1) {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'h_api_key' => 'ed44559c987ef8fbec7b4e1eaa2704353d7907db0dd306bb1a21ea70b3c4dccc',

                ])
                ->post('https://api.mobitechtechnologies.com/sms/sendsms', [
                    "sender_name" => "SAMWESTLTD",
                    "sender_id" => 0,
                    "response_type" => "json",
                    "mobile" => $customerData->Phone1,
                    'message' => $message,
                ]);
        }

        return;
    }

    public function getSmsDetails()
    {
        $details = [
            'api_key' => \Config::get('services.sms.samwest_sms_api_key'),
            'username' => \Config::get('services.sms.samwest_sms_username'),
            'SenderID' => \Config::get('services.sms.samwest_sender_id'),
        ];

        return $details;
    }

    public function getSmsEndPoints()
    {
        return "https://api.mobitechtechnologies.com/sms/sendsms";
    }

    public function costCentersMapping($id)
    {
        $costingCode = OOCR::where('id', $id)->first();

        if (!$costingCode) {
            return null;
        }
        return $costingCode->OcrCode;
    }

    public function hideTableRowsFieldsPerDocument($ObjType, $tableRow)
    {
        if ($ObjType == 205) {
            if ($tableRow->value == "ItemCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "ItemCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "DiscPrcnt") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "WhsCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "WhsCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "UomCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "Rate") {
                $tableRow->Visible = "N";
            }
        }

        // if ($ObjType == 66) {

        //     if ($tableRow->value == "WhsName") {
        //         $tableRow->Visible = "N";
        //     }

        //     if ($tableRow->value == "FromWhsCod") {
        //         $tableRow->Visible = "Y";
        //     }

        //     if ($tableRow->value == "ToWhsCode") {
        //         $tableRow->Visible = "Y";
        //     }

        //     if ($tableRow->value == "DiscPrcnt") {
        //         $tableRow->Visible = "N";
        //     }
        // }

        return $tableRow;
    }

    public function getOpeningBalanceNumberingSeries($ObjectCode)
    {
        $nnm1 = NNM1::where('ObjectCode', $ObjectCode)
            ->where('SeriesName', "OB")
            ->first();
        return $nnm1;
    }


    public function getDocumentForDirectPostingToSAP($ObjType, $DocEntry)
    {
        $isDraft = 0;
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if ($DocumentTables->hasExtApproval == 1) {
            $isDraft = 1;
            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }

        try {
            $documents = $DocumentTables->ObjectHeaderTable::whereNull('ExtRef')
                ->where('ObjType', $ObjType)
                ->where('id', $DocEntry)
                ->get();

            if ($ObjType == 205) {
                $ObjType = 1470000113;
            }

            if ($ObjType == 66) {
                $ObjType = 1250000001;
            }
            foreach ($documents as $key => $headerVal) {
                $headerVal->ExtDocTotal = 0;
                $headerVal->ObjType = $ObjType;
                $headerVal->isDraft = $isDraft;
                $headerVal->DocNum = (int) $headerVal->DocNum;
                $headerVal->DiscPrcnt = 0;
                $headerVal->UserFields  = (object)[
                    "U_CashMail" => $headerVal->U_CashMail,
                    "U_ControlCode" => null,
                    "U_CUInvoiceNum" => null,
                    "U_RelatedInv" => null,
                    "U_ReceiptNo" => null,
                    "U_QRCode" => null,
                    "U_QrLocation" => null
                ];
                //Mappding Base Dcoument
                if ($headerVal->BaseEntry && $headerVal->BaseType) {
                    $baseDocDocumentTables = APDI::with('pdi1')
                        ->where('ObjectID', $headerVal->BaseType)
                        ->first();
                    $baseDocument = $baseDocDocumentTables->ObjectHeaderTable::where('id', $headerVal->BaseEntry)->first();
                    if (!$baseDocument->ExtRef) {
                        $documents->forget($key);
                    }
                    $headerVal->BaseEntry = (int) $baseDocument->ExtRef;
                    $headerVal->BaseType = (int) $headerVal->BaseType != 66 ? $headerVal->BaseType : 1250000001;
                }

                if ($headerVal->ReqType == 12) {
                    $headerVal->Requester = User::where('id', $headerVal->Requester)->value('account');
                }
                if ($headerVal->ReqType == 171) {
                    $headerVal->Requester = OHEM::where('id', $headerVal->Requester)->value('empID');
                }
                //Mapping Branch
                $branch = OBPL::where('BPLId', $headerVal->BPLId)->first();
                $headerVal->Branch = $headerVal->BPLId;
                $headerVal->Location = $branch ? $branch->location?->Location : null;
                //Mapping Numbering Series
                $nnm1 = NNM1::where('id', $headerVal->Series)->first();
                $headerVal->Series = $nnm1 ? $nnm1->ExtRef : null;

                // $headerVal->attachments = $this->getDocumentAttachments($ObjType, $headerVal->id);

                $rowData = $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $headerVal->id)->get();

                foreach ($rowData as $key => $val) {
                    //                    dd($val);
                    $val->DiscPrcnt = $val->DiscPrcnt ?? 0;
                    $SerialNumbers = SRI1::where('LineNum', $val->id)
                        ->where(function ($q) use ($isDraft, $headerVal) {
                            if ($isDraft == 1) {
                                $q->where('BaseType', 112);
                            }
                            if ($isDraft == 0) {
                                $q->where('BaseType', $headerVal->ObjType);
                            }
                        })
                        ->where('BaseEntry', $headerVal->id)
                        ->get();
                    foreach ($SerialNumbers as $key => $serial) {
                        $serial->SysNumber = $serial->SysSerial;
                        $serial->DistNumber = OSRN::where('SysNumber', $serial->SysSerial)->where('ItemCode', $serial->ItemCode)->value('DistNumber');
                    }
                    $val->SerialNumbers = $SerialNumbers;
                    if ($val->BaseEntry != null) {
                        $baseDocDocumentTables = APDI::with('pdi1')
                            ->where('ObjectID', $val->BaseType)
                            ->first();
                        $baseDocumentRow = $baseDocDocumentTables->ObjectHeaderTable::where('id', $val->BaseEntry)->first();
                        $val->BaseEntry = (int) $baseDocumentRow->ExtRef;
                        $val->BaseType = (int) $val->BaseType != 66 ? $val->BaseType : 1250000001;
                    }

                    $val->UnitPrice = $val->PriceBefDi;
                    //tax group
                    $taxGroup = TaxGroup::where('category', 'O')->where("code", $val->TaxCode)->first();
                    if ($ObjType == "205") {
                        $taxGroup = TaxGroup::where('category', 'I')->where("code", $val->TaxCode)->first();
                    }
                    // $val->Price = 0;
                    // $val->UnitPrice = 0;
                    //                    $val->DiscPrcnt = $val->DiscPrcnt;
                    $val->VatGroup = $val->TaxCode;
                    $val->VatPrcnt = $taxGroup?->rate;
                    $val->userFields = (object)[
                        "U_HSCode" => $val->U_HSCode,
                    ];
                    $val->UoMEntry = OUOM::Where('id', $val->UomCode)->value('ExtRef') ?? null;
                }
                $headerVal->document_lines = $rowData;

                if ($headerVal->ObjType == 13) {
                    $headerVal->payments = (new BankingDocumentService())->getInvoicePayment($headerVal->id);
                }
            }

            return $documents->first();
        } catch (\Throwable $th) {

            Log::error($th);
            throw $th;
        }
    }

    public function getDocumentForDirectPostingToTims($ObjType, $docEntry)
    {
//        $updated_at = \Request::get('updated_at');
//        $docEntry = \Request::get('docEntry');

//        if ($ObjType == 1470000113) {
//
//            $ObjType = 205;
//        }
//
//        if ($ObjType == 1250000001) {
//
//            $ObjType = 66;
//        }

//        $isDraft = 0;
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

//        if ($DocumentTables->hasExtApproval == 1) {
//            $isDraft = 1;
//            $DocumentTables = APDI::with('pdi1')
//                ->where('ObjectID', 112)
//                ->first();
//        }

        DB::beginTransaction();
        try {
            $headerVal = $DocumentTables->ObjectHeaderTable::where("id",$docEntry)->first();

//            if ($ObjType == 205) {
//                $ObjType = 1470000113;
//            }
//
//            if ($ObjType == 66) {
//                $ObjType = 1250000001;
//            }
            if ($headerVal) {
                /**
                 * Mark The document not transferred
                 */

                $headerVal->ExtDocTotal = 0;
                $headerVal->ObjType = $ObjType;
//                $headerVal->isDraft = $isDraft;
                $headerVal->DocNum = (int) $headerVal->DocNum;
                $headerVal->DiscPrcnt = 0;
                //Mappding Base Dcoument
                if ($headerVal->BaseEntry && $headerVal->BaseType) {
                    $baseDocDocumentTables = APDI::with('pdi1')
                        ->where('ObjectID', $headerVal->BaseType)
                        ->first();
                    $baseDocument = $baseDocDocumentTables->ObjectHeaderTable::where('id', $headerVal->BaseEntry)->first();
//                    if (!$baseDocument->ExtRef) {
//                        $documents->forget($key);
//                    }
                    $headerVal->BaseEntry = (int) $baseDocument->ExtRef;
                    $headerVal->BaseType = (int) $headerVal->BaseType != 66 ? $headerVal->BaseType : 1250000001;
                }

                if ($headerVal->ReqType == 12) {
                    $headerVal->Requester = User::where('id', $headerVal->Requester)->value('account');
                }
                if ($headerVal->ReqType == 171) {
                    $headerVal->Requester = OHEM::where('id', $headerVal->Requester)->value('empID');
                }

                //Mapping Branch
                $branch = OBPL::where('BPLId', $headerVal->BPLId)->first();
                $headerVal->Branch = $headerVal->BPLId;
                $headerVal->Location = $branch ? $branch->location?->Location : null;
                //Mapping Numbering Series
                $nnm1 = NNM1::where('id', $headerVal->Series)->first();
                $headerVal->Series = $nnm1 ? $nnm1->ExtRef : null;

                //get Attachments
                $headerVal->attachments = ITransactionController::getDocumentAttachments($ObjType, $headerVal->id);

                //Custom Udfs

                $userFields = (object)[
                    "U_CashMail" => $headerVal->U_CashMail,
                    "U_ControlCode" => $headerVal->U_ControlCode,
                    "U_RelatedInv" => $headerVal->U_RelatedInv,
                    "U_CUInvoiceNum" => $headerVal->U_CUInvoiceNum,
                    "U_QRCode" => $headerVal->U_QRCode,
                    "U_QrLocation" => $headerVal->U_QrLocation,
                    "U_ReceiptNo" => $headerVal->U_ReceiptNo,
                    "U_CommitedTime" => $headerVal->U_CommitedTime,
                ];

                $headerVal->UserFields = $userFields;

                $rowData = $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $headerVal->id)->get();

                foreach ($rowData as $key => $val) {
                    $val->DiscPrcnt = $val->DiscPrcnt ?? 0;
                    $SerialNumbers = SRI1::where('BaseType', $ObjType)
                        ->where('BaseEntry', $headerVal->id)
                        ->where('LineNum', $val->id)
                        ->get();
                    foreach ($SerialNumbers as $key => $serial) {
                        $serial->SysNumber = $serial->SysSerial;
                        $serial->DistNumber = OSRN::where('SysNumber', $serial->SysSerial)->where('ItemCode', $serial->ItemCode)->value('DistNumber');
                    }
                    $val->SerialNumbers = $SerialNumbers;
                    if ($val->BaseEntry != null) {
                        $baseDocDocumentTables = APDI::with('pdi1')
                            ->where('ObjectID', $val->BaseType)
                            ->first();
                        $baseDocumentRow = $baseDocDocumentTables->ObjectHeaderTable::where('id', $val->BaseEntry)->first();
                        $val->BaseEntry = (int) $baseDocumentRow->ExtRef;
                        $val->BaseType = (int) $val->BaseType != 66 ? $val->BaseType : 1250000001;
                    }

                    $val->UnitPrice = $val->PriceBefDi;
                    $taxGroup = TaxGroup::where('category', 'O')->where("code",$val->TaxCode)->first();
                    if ($ObjType == "205"){
                        $taxGroup = TaxGroup::where('category', 'I')->where("code",$val->TaxCode)->first();
                    }
                    $val->VatPrcnt = $taxGroup?->rate;
                    $val->VatGroup = $val->TaxCode;
                    $val->UoMEntry = OUOM::Where('id', $val->UomCode)->value('ExtRef') ?? null;

                    $val->UserFields = (object)[
                        "U_HSCode"=>null
                    ];
                }
                $headerVal->document_lines = $rowData;

                if ($headerVal->ObjType == 13) {
                    $headerVal->payments = (new BankingDocumentService())->getInvoicePayment($headerVal->id);
                }
            }
            DB::commit();
            return $headerVal;
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th);
            throw $th;
        }
    }
}
