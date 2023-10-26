<?php

namespace Leysco100\MarketingDocuments\Services;

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
    public function BasicValidation($data)
    {
        /**
         * 1. Object Type
         */
        if (!isset($data['ObjType']) || empty($data['ObjType'])) {

            (new ApiResponseService())->apiSuccessAbortProcessResponse("Object Type is Required");
        }
        $data =     $this->headerBasicValidation($data);
        return $data;
    }

    public function fieldsDefaulting($data)
    {

        $user_id = Auth::user()->id;
        $user_data = User::with('oudg')->where('id', $user_id)->first();

        if (!(data_get($data, 'SlpCode'))) {
            $data['SlpCode'] = $user_data->oudg->SalePerson ?? null;
        }
        if (!(data_get($data, 'Ownercode'))) {
            $data['Ownercode'] = $user_data->EmpID ?? null;
        }
        if (!(data_get($data, 'Warehouse'))) {
            $data['Warehouse'] =  $user_data->oudg->Warehouse ?? null;
        }
        if ((data_get($data, 'CardCode'))) {
            $customerDetails = OCRD::where('CardCode', $data['CardCode'])->first();
            if (!(data_get($data, 'CardName'))) {
                if ($customerDetails) {
                    $data['CardName'] = $customerDetails['CardName'];
                }
            }
        }

        if ($data['document_lines']) {
            $documentLines = $data['document_lines'];
            foreach ($documentLines as $key => $line) {
                if (data_get($line, 'ItemCode')) {
                    $itemDetails = OITM::where('ItemCode', $line['ItemCode'])->first();
        
                    if (!data_get($line, 'ItemName')) {
                        if ($itemDetails) {
                            $documentLines[$key]['Dscription'] = $itemDetails['ItemName'];
                        }
                    }
                }
            }
        
            $data['document_lines'] = $documentLines;
        }
        
        
        return $data;
    }
    public function headerBasicValidation($docdata)
    {


        //1. Customer Code
        $ObjType = $docdata['ObjType'] ?? null;
        $CardCode = $docdata['CardCode'] ?? null;

        //
        if ($docdata['BaseType'] && $docdata['BaseEntry']) {
            $baseDocument = (new CommonService())->getSingleDocumentDetails($docdata['BaseType'], $docdata['BaseEntry']);
            if (!$baseDocument) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Base Document Doest Not exist");
            }
            $CardCode = $baseDocument->CardCode;
        }

        /**
         * If the Document is not Purchase headerdata
         */
        if ($ObjType != 205) {
            $businessPartner = OCRD::with('octg')->where('CardCode', $CardCode)->first();
            /**
             * Check if Customer Exist in the Database
             */
            if (!$businessPartner) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer is Required");
            }
            $docdata['CardName'] =  $businessPartner['CardName'];
            $paymentTerms = $businessPartner->octg;
        }

        if ($ObjType == 205) {
            if (!$docdata['ReqType']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("headerdata Type is required");
            }

            if (!$docdata['headerdataer']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("headerdataer is required");
            }

            if (!$docdata['ReqDate']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Specify Required date");
            }
        }

        if ($docdata['DiscPrcnt'] > 100) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
        }

        /**
         * Validating Payments if the customer is cash Customer
         */
        if ($ObjType == 13 && $docdata["payments"]) {
            foreach ($docdata['payments'] as $paymentData) {
                //               $paymentData = $docdata['payments'][0] ?? null;
                if ($paymentTerms) {
                    if ($ObjType == 13 && $paymentTerms->ExtraDays == 0 && $paymentTerms->ExtraMonth == 0) {
                        if (!$docdata['payments']) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Payment details is Required");
                        }

                        if ($docdata['DocTotal'] > $paymentData['TotalPaid']) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Invoice & Receipt Must be paid Exactly");
                        }

                        if (!$paymentData['CashAcct'] && $paymentData['CashSum'] > 0) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Cash GL Is required");
                        }

                        if (!isset($paymentData['CheckAcct']) && $paymentData['CheckSum'] > 0) {
                            if (!isset($paymentData['CheckAcct'])) {
                                (new ApiResponseService())->apiSuccessAbortProcessResponse("Checks GL Is required");
                            }
                        }

                        if ($paymentData['TrsfrSum'] > 0) {
                            if (!isset($paymentData['TrsfrRef'])) {
                                (new ApiResponseService())->apiSuccessAbortProcessResponse('EOH Error - indicate the Bank Transfer/M-Pesa Reference');
                            }

                            if (!isset($paymentData['TrsfrAcct'])) {
                                (new ApiResponseService())->apiSuccessAbortProcessResponse('Account for bank transfer has not been defined');
                            }
                        }

                        if ($docdata['DocTotal'] < $paymentData['TotalPaid']) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Payment Amount is greater than invoice amount");
                        }
                    }
                }

                if ($paymentData) {
                    if ($docdata['DocTotal'] < $paymentData['TotalPaid']) {
                        (new ApiResponseService())->apiSuccessAbortProcessResponse("Payment Amount is greater than invoice amount");
                    }
                    if (!$paymentData['CashAcct'] && $paymentData['CashSum'] > 0) {
                        (new ApiResponseService())->apiSuccessAbortProcessResponse("Cash GL Is required");
                    }

                    if (!$paymentData['CashAcct'] && $paymentData['CashSum'] > 0) {
                        (new ApiResponseService())->apiSuccessAbortProcessResponse("Cash GL Is required");
                    }

                    if (count($paymentData['rct3']) > 0) {
                        foreach ($paymentData['rct3'] as $key => $rct3) {
                            if (!isset($rct3['CreditCard'])) {
                                (new ApiResponseService())->apiSuccessAbortProcessResponse("Credit Card Required");
                            }
                        }
                    }
                }
            }
        }

        //If Base Type Exist
        if ($docdata['BaseType'] && $docdata['BaseEntry']) {
            $generalSettings = OADM::where('id', 1)->value('copyToUnsyncDocs');
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $docdata['BaseType'])
                ->first();
            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $docdata['BaseEntry'])
                ->first();
            $CardCode = $baseDocHeader->CardCode;
            if ($generalSettings == 1 && $baseDocHeader->ExtRef == null) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Copy to is Disable for Documents Pending syncing ");
            }
            if ($baseDocHeader->DocStatus == "C") {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Copying to not Possible, Base Document is closed");
            }
        }

        //If Base Type Exist
        if ($docdata['BaseType'] && $docdata['BaseEntry']) {
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $docdata['BaseType'])
                ->first();

            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $docdata['BaseEntry'])
                ->first();
            $CardCode = $baseDocHeader->CardCode;
        }

        $customerDetails = OCRD::where('CardCode', $CardCode)->first();

        if (!$customerDetails && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Customer Required");
        }

        if (count($docdata['document_lines']) <= 0) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Items is required");
        }

        $docdata = $this->linesBasicValidation($docdata);

        return $docdata;
    }



    public function linesBasicValidation($docdata)
    {
        /**
         * Rows Validation
         */
        foreach ($docdata['document_lines'] as $key => $value) {
            /**
             * OTHER VALIDATIONS
             */
            if ($docdata['DocType'] == "I") {
                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                $value['TaxCode'] = $product->VatGourpSa;

                if (!$product) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Items Required");
                }
            }
            if (isset($value['DiscPrcnt'])) {
                if ($value['DiscPrcnt'] > 100) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
                }
                if ($value['DiscPrcnt'] > 0 && $product->QryGroup61 == "Y") {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Following Item Does not allow discount:");
                }
            }
        }
        return $docdata;
    }

    public function createDoc($data, $TargetTables)
    {
        $user = Auth::user();
        $NewDocDetails = [
            'ObjType' => $data['ObjType'],
            'DocType' => $data['DocType'],
            'DocNum' =>  $data['DocNum'],
            'Series' => $data['Series'],
            'CardCode' => $data['CardCode'] ? $data['CardCode'] : null,
            'Requester' => $data['Requester'],
            'ReqName' =>  $data['ReqName'],
            'ReqType' => $data['ReqType'],
            'Department' => $data['Department'],
            'CardName' => $data['CardName'] ? $data['CardName'] : null,
            'SlpCode' => $data['SlpCode'], // Sales Employee
            'U_SalePipe' => $data['U_SalePipe'], // Sales Pipe Line
            //                'OwnerCode' => $user->EmpID, //Owner Code
            'OwnerCode' => $data['OwnerCode'] ? $data['OwnerCode'] : Auth::user()->EmpID, //Owner Code
            'U_CashMail' => $data['U_CashMail'], //Cash Customer  Email
            'U_CashName' => $data['U_CashName'], //Cash Customer  Name
            'U_CashNo' => $data['U_CashNo'], // Cash Customer No
            'U_IDNo' => $data['U_IDNo'], // Id no
            'NumAtCard' => $data['NumAtCard'] ? $data['NumAtCard'] : null,
            'CurSource' => $data['CurSource'],
            'DocTotal' => $data['DocTotal'],
            'VatSum' => $data['VatSum'] ?? 0,
            'DocDate' => $data['DocDate'], //PostingDate
            'TaxDate' => $data['TaxDate'], //Document Date
            'DocDueDate' => $data['DocDueDate'], // Delivery Date
            'ReqDate' => $data['DocDueDate'],
            'CntctCode' => $data['CntctCode'], //Contact Person
            'AgrNo' => $data['AgrNo'],
            'LicTradNum' => $data['LicTradNum'],
            'BaseEntry' => $data['BaseEntry'] ? $data['BaseEntry'] : null, //BaseKey
            'BaseType' => $data['BaseType'] ? $data['BaseType'] : null, //BaseKey
            'UserSign' => $user->id,
            //Inventory Transaction Values
            'Ref2' => $data['Ref2'] ? $data['Ref2'] : null, // Ref2
            'GroupNum' => $data['GroupNum'] ? $data['GroupNum'] : null, //[Price List]
            'ToWhsCode' => $data['ToWhsCode'] ? $data['ToWhsCode'] : null, //To Warehouse Code
            //SeriesDocument
            'DiscPrcnt' => $data['DiscPrcnt'] ?? 0, //Discount Percentages
            'DiscSum' => $data['DiscSum'], // Discount Sum
            'BPLId' => $data['BPLId'],
            'U_SaleType' => $data['U_SaleType'], // Sale Type
            'Comments' => $data['Comments'], //comments
            'NumAtCard2' => $data['NumAtCard2'],
            'JrnlMemo' => $data['JrnlMemo'], // Journal Remarks
            'UseShpdGd' => $data['UseShpdGd'] ?? "N",
            'Rounding' => $data['Rounding'] ?? "N",
            'RoundDif' => $data['RoundDif'] ?? 0,
            'U_ServiceCall' => $data['U_ServiceCall'],
            'U_DemoLocation' => $data['U_DemoLocation'],
            'U_Technician' => $data['U_Technician'],
            'U_Location' => $data['U_Location'],
            'U_MpesaRefNo' => $data['U_MpesaRefNo'],
            'U_PCash' => $data['U_PCash'],
            'U_transferType' => $data['U_transferType'],
            'U_SSerialNo' => $data['U_SSerialNo'],
            'U_TypePur' => $data['U_TypePur'],
            'U_NegativeMargin' => $data['U_NegativeMargin'],
            'U_BaseDoc' => $data['U_BaseDoc'],
            'DataSource' => "I",
            'ExtRef' => $data['saveToDraft'] ? null : "",
            'ExtRefDocNum' => $data['saveToDraft'] ? null : "",
            'ExtDocTotal' => 0,
        ];
        Log::info([$TargetTables->ObjectHeaderTable]);
        $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetails));
        Log::info(gettype($newDoc));
        $newDoc->save();

        $documentRows = [];

        foreach ($data['document_lines'] as $key => $value) {
            $LineNum = $key;
            //$Dscription = $value['Dscription'];
            $StockPrice = 0;
            $Weight1 = 0;
            $ItemCode = $value['ItemCode'] ?? null;
            if ($data['DocType'] == "I") {
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

                // if (!$value['WhsCode']) {
                //     return (new ApiResponseService())
                //         ->apiFailedResponseService("Warehouse Required");
                // }

                //                    return $BaseTables;

                // If Not Sales Order the Inventory Quantities should be Greater

                // if ($data['checkStockAvailabilty']) {

                //     if ($product->InvntItem == "Y") {
                //         $inventoryDetails = OITW::where('ItemCode', $ItemCode)
                //             ->where('WhsCode', $value['WhsCode'])
                //             ->first();

                //         if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                //             return (new ApiResponseService())
                //                 ->apiFailedResponseService("Insufficient stock for item:" . $value['Dscription']);
                //         }
                //     }
                // }

                //Serial Number Validations
                //                    if ($product->ManSerNum == "Y" && $data['ObjType'] != 17) {
                if ($product->ManSerNum == "Y") {
                    if ($data['ObjType'] == 14 || $data['ObjType'] == 16 || $saveToDraft = true) {
                        if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                            return (new ApiResponseService())
                                ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                        }
                    }

                    if ($data['ObjType'] == 15) {
                        if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                            return (new ApiResponseService())
                                ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                        }
                    }

                    if ($data['ObjType'] == 13 && $data['BaseType'] != 15) {
                        if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                            return (new ApiResponseService())
                                ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                        }
                    }
                }

                if ($value['Quantity'] <= 0) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Invalid quantity   for item:" . $value['Dscription']);
                }

                if ($value['Price'] < 0) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Invalid price for item:" . $value['Dscription']);
                }

                /**
                 * Stock Price
                 */

                $StockPrice = $product->AvgPrice;

                //Weight1
                $Weight1 = $product->SWeight1 * $value['Quantity'];
            }


            if ($data['DocType'] == "I") {
                if (!isset($value['ItemCode'])) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Item Details");
                }
            }

            $copiedFromObjType = $value['copiedFromObjType'] ?? null;
            $copiedFromBaseRef = $value['copiedFromBaseRef'] ?? null;
            $copiedFromBaseEntry = $value['copiedFromBaseEntry'] ?? null;
            $copiedFromBaseLine = $value['copiedFromBaseLine'] ?? null;

            $rowdetails = [
                'DocEntry' => $newDoc->id,
                'OwnerCode' => $data['OwnerCode'] ? $data['OwnerCode'] : Auth::user()->EmpID, //Owner Code
                'LineNum' => $LineNum, //    Row Number
                'ItemCode' => $value['ItemCode'] ?? null,
                'Dscription' =>  $product->Dscription, // Item Description
                'SerialNum' => $value['SerialNum'] ?? NULL, //    Serial No.
                'Quantity' => $value['Quantity'] ?? 1, //    Quantity
                'DelivrdQty' => $value['DelivrdQty'] ?? NULL, //    Delivered Qty
                'InvQty' => $value['InvQty'] ?? NULL, //   Qty(Inventory UoM)
                'OpenInvQty' => $value['OpenInvQty'] ?? NULL,  //Open Inv. Qty ------
                'PackQty' => $value['PackQty'] ?? NULL,  //    No. of Packages
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
                'OcrCode' => $value['OcrCode'] ?? NULL,  //    Dimension 1
                'OcrCode2' => $value['OcrCode2'] ?? NULL, //    Dimension 2
                'OcrCode3' => $value['OcrCode3'] ?? NULL,  //    Dimension 3
                'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                'CogsOcrCod' => $value['OcrCode'] ?? NULL,
                'CogsOcrCo2' => $value['OcrCode2'] ?? NULL,
                'CogsOcrCo3' => $value['OcrCode3'] ?? NULL,
                'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                'CogsOcrCo5' => $value['OcrCode5'] ?? null,

                'BaseType' => $data['BaseType'] ?? $copiedFromObjType, //    Base Type
                'BaseRef' => $data['BaseRef'] ?? $copiedFromBaseRef, //    Base Ref.
                'BaseEntry' => $data['BaseEntry'] ?? $copiedFromBaseEntry, //    Base Key
                'BaseLine' => $value['BaseLine'] ?? $copiedFromBaseLine, //    Base Row
                'VatSum' => $value['VatSum'] ?? 0, //    Tax Amount (LC)

                'UomCode' => $value['UomCode'] ?? null, //    UoM Code
                'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null, //    UoM Name
                'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                'GTotal' => $value['GTotal'] ?? 0, //    Gross Total

                //Inventory Transaction  Value
                'PQTReqDate' => $data['ReqDate'],
                'FromWhsCod' => $value['FromWhsCod'] ?? null, // // From Warehouse Code
                'BPLId' => $data['BPLId'],
                'U_StockWhse' => isset($value['U_StockWhse']) ? $value['U_StockWhse'] : null,
                'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                'StockPrice' => $StockPrice,
                'NoInvtryMv' => $value['NoInv,  utryMv'] ?? "N",
                'U_Promotion' => $value['U_Promotion'] ?? 'Charged',

                //Weight
                'Weight1' => $Weight1,

            ];

            $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
            $rowItems->save();

            //     /**DocType
            //      * Saving Serial Numbers
            //      */

            //     if ($data['DocType'] == "I" && $product->ManSerNum == "Y") {
            //         $saveSerialDetails = false;
            //         if ($request['ObjType'] == 14 || $request['ObjType'] == 16 || $request['ObjType'] == 17) {
            //             $saveSerialDetails = true;
            //         }
            //         if ($request['ObjType'] == 15) {
            //             $saveSerialDetails = true;
            //         }
            //         if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
            //             $saveSerialDetails = true;
            //         }
            //         if ($saveSerialDetails) {
            //             foreach ($value['SerialNumbers'] as $key => $serial) {
            //                 $LineNum = $key;
            //                 SRI1::create([
            //                     "ItemCode" => $ItemCode,
            //                     "SysSerial" => $serial['SysNumber'] ?? $serial['SysSerial'],
            //                     "LineNum" => $rowItems->id,
            //                     "BaseType" => $saveToDraft ? 112 : $ObjType,
            //                     "BaseEntry" => $newDoc->id,
            //                     "CardCode" => $CardCode,
            //                     "CardName" => $customerDetails->CardName,
            //                     "WhsCode" => $value['WhsCode'],
            //                     "ItemName" => $Dscription,
            //                 ]);
            //             }
            //         }
            //     }

            //     if ($request['BaseType'] && $request['BaseEntry']) {
            //         $baseDocHeader->update([
            //             'DocStatus' => "C",
            //         ]);
            //     }
            //     array_push($documentRows, $rowItems);
            // }

            //Stored Procedure Validations

            $objectTypePassedToTns = $data['ObjType'];

            // if ($TargetTables->ObjectID == 112) {
            //     $objectTypePassedToTns = 112;
            // }

            // $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($objectTypePassedToTns, "A", $newDoc->id);
            // if ($storedProcedureResponse) {
            //     if ($storedProcedureResponse->error != 0) {
            //         return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
            //     }
            // }

            // //Validating Draft using Oringal base type
            // if ($objectTypePassedToTns == 112) {
            //     $mockedDataDraftMessage = (new GeneralDocumentValidationService())->draftValidation($newDoc, $documentRows);
            //     if ($mockedDataDraftMessage) {
            //         return (new ApiResponseService())->apiFailedResponseService($mockedDataDraftMessage);
            //     }
            // }
            // if ($newDoc->ObjType == 13 && $request['payments']) {
            //     foreach ($request['payments'] as $payment) {
            //         $storedProcedureResponse = null;
            //         if ($saveToDraft) {
            //             $newPayment = (new BankingDocumentService())->processDraftIncomingPayment($newDoc, $payment);
            //             $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(140, "A", $newPayment->id);
            //         } else {
            //             $newPayment = (new BankingDocumentService())->processIncomingPayment($newDoc, $payment);
            //             $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(24, "A", $newPayment->id);
            //         }
            //         if ($storedProcedureResponse) {
            //             if ($storedProcedureResponse->error != 0) {
            //                 return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
            //             }
            //         }
            //     }
            // }

            if ($objectTypePassedToTns != 112) {
                NumberingSeries::dispatch($data['Series']);
            }

            // /**
            //  * Compare the Document To BaseDocument
            //  */
            // (new GeneralDocumentService())->comporeRowToBaseRow($TargetTables->ObjectID, $newDoc->id);

            // $newDoc->newObjType = $objectTypePassedToTns;

            // if ($request['serviceCallId']) {
            //     $oscl = OSCL::where('id', $request['serviceCallId'])->first();

            //     if ($oscl->customer != $newDoc->CardCode) {
            //         return (new ApiResponseService())
            //             ->apiFailedResponseService(" C&G Error - Customer Code/Customer Name on JobCard and Expense Documents should be similar!");
            //     }
            //     (new ServiceCallService())->mapServiceCallWithExpenseDocument($objectTypePassedToTns, $newDoc->id, $request['serviceCallId']);
            // }

            //            dd($saveToDraft);
            //            if ($saveToDraft == false) {
            //                (new TransactionInventoryEffectAction())->transactionInventoryEffect($ObjType, $newDoc->id);
            //            }
            (new SystemDefaults())->updateNextNumberNumberingSeries($data['Series']);
            //     DB::connection("tenant")->commit();
            //     //            $documentForDirecPostingToSAP = (new DocumentsService())->getDocumentForDirectPostingToSAP($newDoc->ObjType, $newDoc->id);
            //     //            $newDoc->documentForDirecPostingToSAP = $documentForDirecPostingToSAP;
               return (new ApiResponseService())->apiSuccessResponseService($newDoc);
            // } catch (\Throwable $th) {
            //     //            dd($th);
            //     Log::info($th);
            //     DB::connection("tenant")->rollback();
                 return (new ApiResponseService())->apiFailedResponseService("Process failed, Server Error", $th);
        }
    }

  
  

}
