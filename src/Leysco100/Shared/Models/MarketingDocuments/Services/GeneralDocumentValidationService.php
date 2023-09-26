<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Composer\Autoload\includeFile;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\MarketingDocuments\Services\DatabaseValidationServices;
use Leysco100\MarketingDocuments\Actions\StoredProcedureExternalMethodsAction;

/**
 * Purchase and Marketing Document Validation Service
 */
class GeneralDocumentValidationService
{
    /**
     *  Entry Point for Document Validation
     *
     * @param  \Illuminate\Http\Request  $request
     */

    public function documentHeaderValidation($request)
    {
        $resultCode = 1500;
        $resultState = false;
        $message = "Process failed.Try Again later";
        /**
         * --------------------------------------------------------------------------------------------
         * BUSINESS PARTNER DETAILS VALIDATION
         * ---------------------------------------------------------------------------------------------
         */

        //Necessary Validations
        //1. Customer Code
        $ObjType = $request['ObjType'];
        $CardCode = $request['CardCode'];
        $CardName = $request['CardName'];

        //
        if ($request['BaseType'] && $request['BaseEntry']) {
            $baseDocument = (new CommonService())->getSingleDocumentDetails($request['BaseType'], $request['BaseEntry']);
            if (!$baseDocument) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Base Document Doest Not exist");
            }
            $CardCode = $baseDocument->CardCode;
            $CardName = $baseDocument->CardName;
        }

        /**
         * If the Document is not Purchase Request
         */
        if ($ObjType != 205) {
            $businessPartner = OCRD::with('octg')->where('CardCode', $CardCode)->first();
            /**
             * Check if Customer Exist in the Database
             */
            if (!$businessPartner) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer is Required");
            }

            if (!isset($request['U_CashName'])) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Cash Customer is Required");
            }

            if ($request['U_CashName']) {
                if (strlen($request['U_CashName']) > 50) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Cash Customer is incorrect");
                }
                $validate_U_CashName = StoredProcedureExternalMethodsAction::ValidateName($request['U_CashName']);
                if (!$validate_U_CashName) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("CG - Ensure you have Captured A Valid Cash Customer Name with atleast 2 Names e.g \"Ali Tom\"");
                }
            }

            if (!isset($request['U_CashNo'])) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer Phone is Required");
            }

            if ($request['U_CashNo']) {
                if (strlen($request['U_CashNo']) < 10) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer Phone is incorrect");
                }
                $validate_U_CashNo = StoredProcedureExternalMethodsAction::ValidateMobileNumber($request['U_CashNo']);
                if (!$validate_U_CashNo) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("CG - Ensure you have Captured A Valid Mobile Phone Number e.g. +2547**123***");
                }
            }

            //            if (!isset($request['U_IDNo'])) {
            //                (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer ID No is Required");
            //            }

            if ($request['U_IDNo']) {
                if (!is_numeric($request['U_IDNo'])) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer ID is incorrect");
                }
            }

            if (!$request['U_CashMail']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer Email is Required");
            }

            if ($request['U_CashMail']) {
                $validate_U_CashMail = StoredProcedureExternalMethodsAction::ValidateEmail($request['U_CashMail']);
                if (!$validate_U_CashMail) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("CG - Ensure you have Captured A Valid email Adress e.g.\"geff@gmail.com\", or indicate N/A where email does not exist");
                }
            }

            $paymentTerms = $businessPartner->octg;

            /**
             * Validating Dates
             */
            if (!$request['DocDueDate']) {
                if ($ObjType != 13) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Delivery Date Required");
                }
                if ($ObjType == 13) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Due Date Required");
                }
            }
        }

        if ($ObjType == 205) {
            if (!$request['ReqType']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Request Type is required");
            }

            if (!$request['Requester']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Requester is required");
            }

            if (!$request['ReqDate']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Specify Required date");
            }

            if (!$request['U_PCash']) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Specify Petty Cash Request");
            }
        }

        if ($request['DiscPrcnt'] > 100) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
        }

        /**
         * Rows Validation
         */
        // $this->documentRowValidation($request);

        /**
         * Validating Payments if the customer is cash Customer
         */
        if ($ObjType == 13 && $request["payments"]) {
            foreach ($request['payments'] as $paymentData){
                //               $paymentData = $request['payments'][0] ?? null;
                if ($paymentTerms) {
                    if ($ObjType == 13 && $paymentTerms->ExtraDays == 0 && $paymentTerms->ExtraMonth == 0) {
                        if (!$request['payments']) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Payment details is Required");
                        }

                        if ($request['DocTotal'] > $paymentData['TotalPaid']) {
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

                        if ($request['DocTotal'] < $paymentData['TotalPaid']) {
                            (new ApiResponseService())->apiSuccessAbortProcessResponse("Payment Amount is greater than invoice amount");
                        }
                    }
                }

                if ($paymentData) {
                    if ($request['DocTotal'] < $paymentData['TotalPaid']) {
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

        if (count($request['document_lines']) <= 0) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Items is required");
        }

        $this->documentRowValidation($request);
    }

    /**
     * Validating Document Row
     */
    public function documentRowValidation($request)
    {
        foreach ($request['document_lines'] as $key => $value) {
            $ItemCode = null;
            $Dscription = $value['Dscription'];

            if ($value['DiscPrcnt'] > 100) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Invalid Discount Percentage");
            }
            if (!isset($value['TaxCode'])) {
                (new ApiResponseService())->apiSuccessAbortProcessResponse("Select Tax Code for item " . $value['Dscription']);
            }

            /**
             * OTHER VALIDATIONS
             */

            if ($request['DocType'] == "I") {
                $product = OITM::Where('ItemCode', $value['ItemCode'])
                    ->first();
                if (!$product) {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Items Required");
                }

                if ($value['DiscPrcnt'] > 0 && $product->QryGroup61 == "Y") {
                    (new ApiResponseService())->apiSuccessAbortProcessResponse("Following Item Does not allow discount:" . $value['Dscription']);
                }
            }
        }
    }

    /**
     * Validating Document Row
     */
    public function draftValidation($draftHeader, $draftRows)
    {
        DB::connection("tenant")->beginTransaction();
        try {
            $targetTables = APDI::with('pdi1')
                ->where('ObjectID', $draftHeader->ObjType)
                ->first();

            //Getting New Document Number for the document
            $DocNum = (new DocumentsService())
                ->documentNumberingService(
                    $draftHeader->DocNum,
                    $draftHeader->Series
                );

            //Assigning Document Draft Key and DocNum
            $draftHeader->DocNum = $DocNum;
            $draftHeader->draftKey = $draftHeader->id;

            $newDoc = new $targetTables->ObjectHeaderTable($draftHeader->toArray());
            $newDoc->save();

            foreach ($draftRows as $key => $row) {
                $row->DocEntry = $newDoc->id;
                $rowItems = new $targetTables->pdi1[0]['ChildTable']($row->toArray());
                $rowItems->save();
            }

            $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($newDoc->ObjType, "A", $newDoc->id);

            $message = null;
            if ($storedProcedureResponse) {
                if ($storedProcedureResponse->error != 0) {
                    $message = $storedProcedureResponse->error_message;
                }
            }

            DB::connection("tenant")->rollback();
            return $message;
        } catch (\Throwable $th) {
            Log::info($th);
            DB::connection("tenant")->rollback();
        }
    }
}
