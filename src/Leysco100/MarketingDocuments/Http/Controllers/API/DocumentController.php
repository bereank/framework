<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;

use Illuminate\Http\Request;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\OSCL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Leysco100\Shared\Services\UserFieldsService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Banking\Models\PDF2;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\MarketingDocuments\Models\ATC1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OATC;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Actions\TransactionInventoryEffectAction;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\Shared\Models\Shared\Services\ServiceCallService;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\MarketingDocuments\Services\DatabaseValidationServices;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentService;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentValidationService;


class DocumentController extends Controller
{
    /**
     * Get all Documents for specific Object
     * @param Int $ObjType
     * @return \Illuminate\Http\Response
     */
    public function getDocData(Request $request, $ObjType)
    {
        $isDoc = \Request::get('isDoc');
        $docNum = \Request::get('docNum');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 50);

        $createdBy = Auth::user()->id;
        if (\Request::get('created_by')) {
            $createdBy = \Request::get('created_by');
        }

        $tableObjType = $ObjType;
        if ($isDoc == 0) {
            $tableObjType = 112;
        }
        $StartDate = $request['StartDate'];
        $EndDate = $request['EndDate'];
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $tableObjType)
            ->first();
        // (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'read');

        try {
            $data = $DocumentTables->ObjectHeaderTable::where('ObjType', $ObjType)
                ->with('CreatedBy.ohem')
                // ->where(function ($q) use ($StartDate, $EndDate) {
                //     if ($StartDate && $EndDate) {
                //         $q->whereBetween('DocDueDate', [$StartDate, $EndDate]);
                //     }
                // })
                // ->where(function ($q) use ($isDoc) {
                //     if ($isDoc == 2) {
                //         $q->where('DataSource', "E")->where('DocStatus', "O");
                //     }
                // })
                // ->where(function ($q) use ($isDoc) {
                //     if ($isDoc == 0) {
                //         $q->where('DocStatus', "O");
                //     }
                // })
                // ->where(function ($q) use ($isDoc, $createdBy, $docNum) {
                //     if ($isDoc != 2 && !$docNum) {
                //         $q->where('UserSign', $createdBy);
                //     }
                // })
                // ->where(function ($q) use ($docNum) {
                //     if ($docNum) {
                //         $q->where('DocNum', $docNum);
                //     }
                // })
                // ->orderBy('id', 'desc')
                // ->take(100)
                // ->get();
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            if ($ObjType != 205) {
                $data = $DocumentTables->ObjectHeaderTable::select('id', 'CardCode', 'DocNum', 'CardName', 'ExtRef', 'ExtRefDocNum', 'UserSign', 'SlpCode', 'DataSource', 'DocStatus', 'ObjType', 'OwnerCode', 'DocDate', 'DocTotal', 'created_at')
                    ->where('ObjType', $ObjType)
                    ->with('CreatedBy:name', 'ohem:id,empID,firstName,middleName,lastName')
                    ->where(function ($q) use ($StartDate, $EndDate) {
                        if ($StartDate && $EndDate) {
                            $q->whereBetween('DocDueDate', [$StartDate, $EndDate]);
                        }
                    })
                    ->where(function ($q) use ($isDoc) {
                        if ($isDoc == 2) {
                            $q->where('DataSource', "E")->where('DocStatus', "O");
                        }
                    })
                    ->where(function ($q) use ($isDoc) {
                        if ($isDoc == 1) {
                            $q->where('DataSource', '!=', "E");
                        }
                    })
                    ->where(function ($q) use ($isDoc) {
                        if ($isDoc == 0) {
                            $q->where('DocStatus', "O");
                        }
                    })
                    ->where(function ($q) use ($isDoc, $createdBy, $docNum) {
                        if ($isDoc != 2 && !$docNum) {
                            $showAll = false;

                            if ($createdBy == 1) {
                                $showAll = true;
                            }

                            if ($createdBy == 9) {
                                $showAll = true;
                            }

                            if (!$showAll) {
                                //                                $q->where('UserSign', $createdBy);
                                $q->where('OwnerCode', Auth::user()->EmpID);
                            }
                        }
                    })
                    ->where(function ($q) use ($docNum) {
                        if ($docNum) {
                            $q->where('DocNum', $docNum)->orWhere('ExtRefDocNum', $docNum);
                        }
                    })
                    // ->take(100)
                    // ->orderBy('id', 'desc')
                    // ->get();
                    ->latest()
                    ->paginate($perPage, ['*'], 'page', $page);
            }
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
     *
     * Get Single Document
     * @param Int $ObjType
     * @param Int $DocEntry => Document Auto Increment ID
     *
     * @return \Illuminate\Http\Response
     */
    public function getSingleDocData($ObjType, $DocEntry)
    {

        $isDoc = \Request::get('isDoc');
        $isForPrint = \Request::get('isForPrint');
        $copyTo = \Request::get('copyTo');

        if (!$isDoc) {
            $isDoc = 1;
        }

        $originalObjType = $ObjType;
        $isDraft = 0;

        if ($isDoc == 0) {
            $isDraft = 1;
            $ObjType = 112;
        }

        if ($isDoc == 0 && $isForPrint == 1) {
            return (new ApiResponseService())->apiFailedResponseService("Printing disable for draft documents");
        }

        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        //        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'view');

        //        $data = $DocumentTables->ObjectHeaderTable::with('objecttype', 'department', 'document_lines.taxgroup', 'branch', 'CreatedBy', 'location')
        //            ->where('id', $DocEntry)
        //            ->first();

        $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)
            ->with("document_lines.oitm")
            ->first();

        $userFields = (object)[
            "ControlCode" => $data->U_ControlCode,
            "RelatedInvNum" => $data->U_RelatedInv,
            "CUInvNum" => $data->U_CUInvoiceNum,
            "RAuthorityURL" => $data->U_QRCode,
            // "U_QrLocation" => $data->U_QrLocation,
            "ReceiptNo" => $data->U_ReceiptNo,
            // "U_CommitedTime" => $data->U_CommitedTime,
            "DeviceSerialNo" => null,
            "BaseInvNum" => $data->id
        ];

        //        $data['doctype'] = $ObjType;
        //        if ($data) {
        //            $record = (new UserFieldsService())->processUDF($data);
        //        }
        //        $userFields = (object)[];
        //        if ($record) {
        //            foreach ($record['HeaderUserFields'] as $headerField) {
        //                $userFields->{$headerField['FieldName']} = $headerVal->{$headerField['FieldName']};
        //            }
        //
        //            $headerVal->UserFields = $userFields;
        //        }




        $rows = $data->document_lines;

        $generalObjects = [205, 66];

        if (!in_array($ObjType, $generalObjects)) {
            //            $data = $DocumentTables->ObjectHeaderTable::with(
            //                'CreatedBy',
            //                'location',
            //                'BusinessPartner.octg',
            //                'branch.location',
            //                'objecttype',
            //                'oslp',
            //                'document_lines.oitm',
            //                'document_lines.unitofmeasure',
            //                'document_lines.oitm.itm1',
            //                'document_lines.oitm.oitw',
            //                'document_lines.oitm.inventoryuom',
            //                'document_lines.oitm.ougp.ouom',
            //                'document_lines.oitm.oitb',
            //                'document_lines.taxgroup'
            //            )
            //                ->where('id', $DocEntry)
            //                ->first();
            $data = $DocumentTables->ObjectHeaderTable::with(
                'document_lines.oitm',
                'BusinessPartner.octg',
                'CreatedBy'
            )->where('id', $DocEntry)
                ->first();
            $rows = $data->document_lines;
        }

        foreach ($rows as $key => $row) {
            $serialNumbers = SRI1::where('BaseType', $ObjType)
                ->where('BaseEntry', $data->id)
                ->where('LineNum', $row->id)
                ->get();
            if ($isForPrint && $row->BaseType) {
                /**
                 * Get Base Row ID
                 */
                $baseRow = (new GeneralDocumentService())
                    ->getBaseLineDetails($row->BaseType, $row->BaseEntry, $row->BaseLine);
                /**
                 * Get Base Details
                 */
                $baseSerialNumbers = (new GeneralDocumentService())
                    ->getDocumentLinesSerialNumbers($row->BaseType, $row->BaseEntry, $baseRow->id);

                //                $serialNumbers = $serialNumbers->merge($baseSerialNumbers);
                $serialNumbers = $serialNumbers->merge($baseSerialNumbers)->unique('SysSerial')->values()->all();
            }
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
            if ($copyTo) {

                $row->BaseDocObj = (object)[
                    "id" => $data->id,
                    "ExtRef" => $data->ExtRef,
                ];
                $row->BaseLineObj = (object)[
                    "DocEntry" => $row->id,
                    "LineNum" => $row->LineNum
                ];
            }
            $row->oitm = $row->oitm()->select("UgpEntry", "SUoMEntry")->get()->first();
            $taxGroup = TaxGroup::where('category', 'O')->where("code", $row->TaxCode)->first();
            if ($ObjType == "205") {
                $taxGroup = TaxGroup::where('category', 'I')->where("code", $row->TaxCode)->first();
            }

            $row->VatGroup = $row->TaxCode;
            $row->VatPrcnt = $taxGroup?->rate;

            $row->UserFields = (object)[
                "U_HSCode" => null
            ];
        }

        $oats = ATC1::where('AbsEntry', $data->AtcEntry)
            ->get();
        foreach ($oats as $key => $file) {
            $file->realPath = asset($file->Path);
        }
        $data->oats = $oats;
        $data->isDoc = $isDoc;
        $data->isDraft = $isDraft;

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

        if ($copyTo) {
            $data->makeHidden(["id", "ExtRef", "ExtRefDocNum", "ExtDocTotal"]);
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

        /**
         * Showing Invoice Payments
         *
         */

        $data->payments = [];
        if ($originalObjType == 13) {
            //              $data->qrcode = "data:image/jpeg;base64," . base64_encode(QrCode::format('png')->size(100)->generate('LS100-AR-' . $data->DocNum));
            $data->gatepass_qrcode = "data:image/jpeg;base64," . base64_encode(QrCode::format('png')->size(100)->generate('LS100-AR-' . $data->DocNum));
            //check if fiscalized if not create data for direct posting to tims
            if ($data->U_QRCode) {
                $data->qrcode = "data:image/jpeg;base64," . base64_encode(QrCode::format('png')->size(100)->generate($data->U_QRCode));
            } else {
                $data->timsPayload =  (new DocumentsService())->getDocumentForDirectPostingToTims($ObjType, $DocEntry);
            }
            if ($isDoc == 0) {
                $data->payments = PDF2::with('opdf')->where('DocEntry', $data->id)->get();
            } else {
                $data->payments = RCT2::with('orct')->where('DocEntry', $data->id)->get();
            }
        }

        if ($ObjType == 15) {
            // $data->qrcode = "data:image/jpeg;base64," . base64_encode(QrCode::format('png')->size(100)->generate('LS100-DN-' . $data->DocNum));
        }
        $data->UserFields = $userFields;
        return $data;
    }

    public function updateSingleDocData(Request $request, $ObjType, $DocEntry)
    {
        $status = $request['status'];
        $isDoc = $request['isDoc'];
        $ObjType = $ObjType;
        if ($isDoc == 0) {
            $ObjType = 112;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        //        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'write');

        $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)->first();
        $data->update([
            "Transfered" => $status
        ]);
        return $data;
    }
    /**
     *
     * Creating Document
     * @param Int $ObjType is required to identify the table
     *
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ObjType' => 'required',
        ]);

        //initiate the api response service
        $apiResponseService = new ApiResponseService();

        (new GeneralDocumentValidationService())->documentHeaderValidation($request);

        //Necessary Validations

        //1. Customer Code
        $CardCode = $request['CardCode'];
        //If Base Type Exist
        if ($request['BaseType'] && $request['BaseEntry']) {
            $generalSettings = OADM::where('id', 1)->value('copyToUnsyncDocs');
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $request['BaseType'])
                ->first();
            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $request['BaseEntry'])
                ->first();
            $CardCode = $baseDocHeader->CardCode;
            if ($generalSettings == 1 && $baseDocHeader->ExtRef == null) {
                return $apiResponseService
                    ->apiFailedResponseService("Copy to is Disable for Documents Pending syncing ");
            }
            if ($baseDocHeader->DocStatus == "C") {
                return $apiResponseService
                    ->apiFailedResponseService("Copying to not Possible, Base Document is closed");
            }
        }

        $user = Auth::user();
        $ObjType = (int) $request['ObjType'];

        $saveToDraft = false;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$TargetTables) {
            return $apiResponseService
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        /**
         * Check if The Item has External Approval
         */
        if ($TargetTables->hasExtApproval == 1) {
            $saveToDraft = true;
            $TargetTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }

        /**
         * Check If Authorized
         */
        //        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'write');

        //If Base Type Exist
        if ($request['BaseType'] && $request['BaseEntry']) {
            $BaseTables = APDI::with('pdi1')
                ->where('ObjectID', $request['BaseType'])
                ->first();

            $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $request['BaseEntry'])
                ->first();
            $CardCode = $baseDocHeader->CardCode;
        }

        $customerDetails = OCRD::where('CardCode', $CardCode)->first();
        if (!$customerDetails && $ObjType != 205) {
            return $apiResponseService
                ->apiFailedResponseService("Customer Required");
        }

        if ($request['DiscPrcnt'] > 100) {
            return $apiResponseService
                ->apiFailedResponseService("Invalid Discount Percentage");
        }

        /**
         * Mapping Req Name
         */
        $ReqName = null;
        if ($ObjType == 205) {
            if ($request['ReqType'] == 12) {
                $ReqName = User::where('id', $request['Requester'])->value('name');
            }

            if ($request['ReqType'] == 171) {
                $employee = OHEM::where('id', $request['Requester'])->first();
                $ReqName = $employee->firstName . " " . $employee->lastName;
            }
        }

        $checkStockAvailabilty = false;

        if (($ObjType == 13 && $request['BaseType'] != 15) || $ObjType == 15) {
            $checkStockAvailabilty = true;
        }



        DB::connection("tenant")->beginTransaction();
        try {


            /**
             * Handling  Document Numbering
             */
            $DocNum = (new DocumentsService())
                ->documentNumberingService(
                    $request['DocNum'],
                    $request['Series']
                );


            $NewDocDetails = [
                'ObjType' => $request['ObjType'],
                'DocType' => $request['DocType'],
                'DocNum' => $DocNum,
                'Series' => $request['Series'],
                'CardCode' => $CardCode ? $CardCode : null,
                'Requester' => $request['Requester'],
                'ReqName' => $ReqName,
                'ReqType' => $request['ReqType'],
                'Department' => $request['Department'],
                'CardName' => $customerDetails ? $customerDetails->CardName : null,
                'SlpCode' => $request['SlpCode'], // Sales Employee
                //                'OwnerCode' => $user->EmpID, //Owner Code
                'OwnerCode' => $request['OwnerCode'], //Owner Code
                'NumAtCard' => $request['NumAtCard'] ? $request['NumAtCard'] : null,
                'CurSource' => $request['CurSource'],
                'DocTotal' => $request['DocTotal'],
                'VatSum' => $request['VatSum'] ?? 0,
                'DocDate' => $request['DocDate'], //PostingDate
                'TaxDate' => $request['TaxDate'], //Document Date
                'DocDueDate' => $request['DocDueDate'], // Delivery Date
                'ReqDate' => $request['DocDueDate'],
                'CntctCode' => $request['CntctCode'], //Contact Person
                'AgrNo' => $request['AgrNo'],
                'LicTradNum' => $request['LicTradNum'],
                'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //BaseKey
                'BaseType' => $request['BaseType'] ?? "1", //BaseKey
                'UserSign' => $user->id,
                //Inventory Transaction Values
                'Ref2' => $request['Ref2'] ? $request['Ref2'] : null, // Ref2
                'GroupNum' => $request['GroupNum'] ? $request['GroupNum'] : null, //[Price List]
                'ToWhsCode' => $request['ToWhsCode'] ? $request['ToWhsCode'] : null, //To Warehouse Code
                //SeriesDocument
                'DiscPrcnt' => $request['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' => $request['DiscSum'], // Discount Sum
                'BPLId' => $request['BPLId'],
                'Comments' => $request['Comments'], //comments
                'NumAtCard2' => $request['NumAtCard2'],
                'JrnlMemo' => $request['JrnlMemo'], // Journal Remarks
                'UseShpdGd' => $request['UseShpdGd'] ?? "N",
                'Rounding' => $request['Rounding'] ?? "N",
                'RoundDif' => $request['RoundDif'] ?? 0,
                'DataSource' => "I",
                'ExtRef' => $saveToDraft ? null : "N/A",
                'ExtRefDocNum' => $saveToDraft ? null : "N/A",
                'ExtDocTotal' => 0,
            ];

            $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetails));

            $newDoc->save();

            if (is_array($request->UserFields)) {
                $newDoc->update($request->UserFields);
            }
            $documentRows = [];


            foreach ($request['document_lines'] as $key => $value) {
                $LineNum = $key;
                $Dscription = $value['Dscription'];
                $StockPrice = 0;
                $Weight1 = 0;

                if ($request['DocType'] == "I") {
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

                    if (!$value['WhsCode']) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Warehouse Required");
                    }

                    //                    return $BaseTables;

                    // If Not Sales Order the Inventory Quantities should be Greater

                    if ($checkStockAvailabilty) {

                        if ($product->InvntItem == "Y") {
                            $inventoryDetails = OITW::where('ItemCode', $ItemCode)
                                ->where('WhsCode', $value['WhsCode'])
                                ->first();

                            if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Insufficient stock for item:" . $value['Dscription']);
                            }
                        }
                    }

                    //Serial Number Validations
                    //                    if ($product->ManSerNum == "Y" && $request['ObjType'] != 17) {
                    if ($product->ManSerNum == "Y") {
                        if ($request['ObjType'] == 14 || $request['ObjType'] == 16 || $saveToDraft = true) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 15) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
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

                if (!isset($Dscription)) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Description Required");
                }

                if ($request['DocType'] == "I") {
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
                    'OwnerCode' => $request['OwnerCode'], //Owner Code
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $value['ItemCode'] ?? null,
                    'Dscription' => $Dscription, // Item Description
                    'SerialNum' => $value['SerialNum'], //    Serial No.
                    'Quantity' => $value['Quantity'] ?? 1, //    Quantity
                    'DelivrdQty' => $value['DelivrdQty'], //    Delivered Qty
                    'InvQty' => $value['InvQty'], //   Qty(Inventory UoM)
                    'OpenInvQty' => $value['OpenInvQty'], //Open Inv. Qty ------
                    'PackQty' => $value['PackQty'], //    No. of Packages
                    'Price' => $value['Price'] ?? 0, //    Price After Discount
                    'DiscPrcnt' => $value['DiscPrcnt'] ?? 0, //    Discount %
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : 0, //    Rate
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                    'PriceAfVAT' => $value['PriceAfVAT'] ?? 0, //       Gross Price after Discount
                    'PriceBefDi' => $value['PriceBefDi'] ?? 0, // Unit Price
                    'LineTotal' => $value['LineTotal'] ?? $value['Price'], //    Total (LC)
                    'WhsCode' => $value['WhsCode'] ?? null, //    Warehouse Code
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                    'SlpCode' => $request['SlpCode'], //    Sales Employee
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                    'OcrCode' => $value['OcrCode'], //    Dimension 1
                    'OcrCode2' => $value['OcrCode2'], //    Dimension 2
                    'OcrCode3' => $value['OcrCode3'], //    Dimension 3
                    'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                    'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                    'CogsOcrCod' => $value['OcrCode'],
                    'CogsOcrCo2' => $value['OcrCode2'],
                    'CogsOcrCo3' => $value['OcrCode3'],
                    'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                    'CogsOcrCo5' => $value['OcrCode5'] ?? null,

                    'BaseType' => $request['BaseType'] ?? $copiedFromObjType, //    Base Type
                    'BaseRef' => $request['BaseRef'] ?? $copiedFromBaseRef, //    Base Ref.
                    'BaseEntry' => $request['BaseEntry'] ?? $copiedFromBaseEntry, //    Base Key
                    'BaseLine' => $value['BaseLine'] ?? $copiedFromBaseLine, //    Base Row
                    'VatSum' => $value['VatSum'] ?? 0, //    Tax Amount (LC)

                    'UomCode' => $value['UomCode'] ?? null, //    UoM Code
                    'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null, //    UoM Name
                    'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                    'OwnerCode' => $value['OwnerCode'] ?? null, //    Owner
                    'GTotal' => $value['GTotal'] ?? 0, //    Gross Total

                    //Inventory Transaction  Value
                    'PQTReqDate' => $request['ReqDate'],
                    'FromWhsCod' => $value['FromWhsCod'] ?? null, // // From Warehouse Code
                    'BPLId' => $request['BPLId'],
                    'U_StockWhse' => isset($value['U_StockWhse']) ? $value['U_StockWhse'] : null,
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                    'StockPrice' => $StockPrice,
                    'NoInvtryMv' => $value['NoInvtryMv'] ?? "N",
                    'U_Promotion' => $value['U_Promotion'] ?? 'Charged',

                    //Weight
                    'Weight1' => $Weight1,

                ];

                $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
                $rowItems->save();

                /**DocType
                 * Saving Serial Numbers
                 */

                if ($request['DocType'] == "I" && $product->ManSerNum == "Y") {
                    $saveSerialDetails = false;
                    if ($request['ObjType'] == 14 || $request['ObjType'] == 16 || $request['ObjType'] == 17) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 15) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
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
                                "CardCode" => $CardCode,
                                "CardName" => $customerDetails->CardName,
                                "WhsCode" => $value['WhsCode'],
                                "ItemName" => $Dscription,
                            ]);
                        }
                    }
                }

                if ($request['BaseType'] && $request['BaseEntry']) {
                    $baseDocHeader->update([
                        'DocStatus' => "C",
                    ]);
                }
                array_push($documentRows, $rowItems);
            }

            //Stored Procedure Validations

            $objectTypePassedToTns = $request['ObjType'];

            if ($TargetTables->ObjectID == 112) {
                $objectTypePassedToTns = 112;
            }

            //            $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($objectTypePassedToTns, "A", $newDoc->id);
            //            if ($storedProcedureResponse) {
            //                if ($storedProcedureResponse->error != 0) {
            //                    return $apiResponseService->apiFailedResponseService($storedProcedureResponse->error_message);
            //                }
            //            }

            //Validating Draft using Oringal base type
            //            if ($objectTypePassedToTns == 112) {
            //                $mockedDataDraftMessage = (new GeneralDocumentValidationService())->draftValidation($newDoc, $documentRows);
            //                if ($mockedDataDraftMessage) {
            //                    return $apiResponseService->apiFailedResponseService($mockedDataDraftMessage);
            //                }
            //            }

            if ($newDoc->ObjType == 13 && $request['payments']) {
                $bankingDocumentService = new BankingDocumentService();
                foreach ($request['payments'] as $payment) {
                    $storedProcedureResponse = null;
                    if ($saveToDraft) {
                        $newPayment = $bankingDocumentService->processDraftIncomingPayment($newDoc, $payment);
                        //                        $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(140, "A", $newPayment->id);
                    } else {
                        $newPayment = $bankingDocumentService->processIncomingPayment($newDoc, $payment);
                        //                        $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions(24, "A", $newPayment->id);
                    }
                    if ($storedProcedureResponse) {
                        if ($storedProcedureResponse->error != 0) {
                            return $apiResponseService->apiFailedResponseService($storedProcedureResponse->error_message);
                        }
                    }
                }
            }

            if ($objectTypePassedToTns != 112) {
                NumberingSeries::dispatch($request['Series']);
            }

            /**
             * Compare the Document To BaseDocument
             */
            (new GeneralDocumentService())->comporeRowToBaseRow($TargetTables->ObjectID, $newDoc->id);

            $newDoc->newObjType = $objectTypePassedToTns;

            if ($request['serviceCallId']) {
                $oscl = OSCL::where('id', $request['serviceCallId'])->first();

                if ($oscl->customer != $newDoc->CardCode) {
                    return $apiResponseService
                        ->apiFailedResponseService(" C&G Error - Customer Code/Customer Name on JobCard and Expense Documents should be similar!");
                }
                (new ServiceCallService())->mapServiceCallWithExpenseDocument($objectTypePassedToTns, $newDoc->id, $request['serviceCallId']);
            }

            //            dd($saveToDraft);
            //            if ($saveToDraft == false) {
            //                (new TransactionInventoryEffectAction())->transactionInventoryEffect($ObjType, $newDoc->id);
            //            }
            DB::connection("tenant")->commit();
            //            $documentForDirecPostingToSAP = (new DocumentsService())->getDocumentForDirectPostingToSAP($newDoc->ObjType, $newDoc->id);
            //            $newDoc->documentForDirecPostingToSAP = $documentForDirecPostingToSAP;
            return $apiResponseService->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
            dd($th);
            Log::info($th);
            DB::connection("tenant")->rollback();
            return $apiResponseService->apiFailedResponseService("Process failed, Server Error", $th);
        }
    }
    // saving Attachments
    public function upload(Request $request)
    {
        $ObjType = $request->ObjType;
        $DocEntry = $request->DocEntry;

        if (!$DocEntry && $request->id) {
            $DocEntry = $request->id;
        }

        $ExtRefAtcEntry = $request->ExtRefAtcEntry;

        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        $data = $TargetTables->ObjectHeaderTable::where("id", $DocEntry)
            ->first();
        if (!$data) {
            return (new ApiResponseService())->apiFailedResponseService("Error Document not found");
        }

        if (isset($request['files'])) {
            try {
                $attachment = new OATC();
                $attachment->ExtRef = $ExtRefAtcEntry;
                $attachment->save();

                $data->update([
                    "AtcEntry" => $attachment->id
                ]);
                // Check if the request contains JSON data
                if ($request->isJson()) {
                    foreach ($request['files'] as $key => $value) {
                        $fileUpload = new ATC1();
                        $fileUpload->AbsEntry = $attachment->id;
                        $fileUpload->FileName = $value['file_name'] ?? null;
                        $fileUpload->FileExt = $value['FileExt'] ?? $key;
                        $fileUpload->trgtPath = $value['trgtPath'] ?? null;
                        $fileUpload->Date = date("Y-m-d");
                        $fileUpload->UsrID = Auth::user()->id;
                        $fileUpload->save();
                    }
                } else {
                    foreach ($request['files'] as $key => $value) {
                        $fileUpload = new ATC1();
                        $fileUpload->AbsEntry = $attachment->id;
                        $file_name = $DocEntry . rand(1, 100000) . "." . $value->getClientOriginalExtension();
                        $file_path = $value->storeAs("attachments/" . $ObjType . "/" . $DocEntry, $file_name, 'public');
                        $fileUpload->FileName = $value->getClientOriginalName();
                        $fileUpload->FileExt = $value->getClientOriginalExtension();
                        $fileUpload->trgtPath = '/storage/' . $file_path;
                        $fileUpload->Date = date("Y-m-d");
                        $fileUpload->UsrID = Auth::user()->id;
                        $fileUpload->save();
                    }
                }
                return (new ApiResponseService())->apiSuccessResponseService("Uploaded Successfully");
            } catch (\Throwable $th) {
                Log::info($th);
                return (new ApiResponseService())->apiFailedResponseService("Error uploading attachment".$th->getMessage());
            }
        }
        return (new ApiResponseService())->apiFailedResponseService("No attachment files found");
    }

    //UpdateDocument
    public function updateSingleDocument(Request $request)
    {

        $user = Auth::user();

        $isDoc = \Request::get('isDoc');
        $DocEntry = $request["id"];

        $ObjType = $request["ObjType"];

        if ($isDoc == 0) {
            $ObjType = 112;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $data = $DocumentTables->ObjectHeaderTable::with(
            'document_lines:id,DocEntry,LineNum,ItemCode,Dscription,OwnerCode,SerialNum,Quantity,DelivrdQty,InvQty,OpenInvQty,PackQty,Price,DiscPrcnt,Rate,TaxCode,PriceAfVAT,PriceBefDi,LineTotal,WhsCode,ShipDate,SlpCode,Commission,AcctCode,OcrCode,OcrCode2,OcrCode3,OcrCode4,OcrCode5,OpenQty,GrossBuyPr,GPTtlBasPr,BaseType,BaseRef,BaseEntry,BaseLine,VatSum,GrssProfit,PoTrgNum,OrigItem,BackOrdr,FreeTxt,TrnsCode,UomCode,unitMsr,NumPerMsr,Text,GTotal,AgrNo,CogsOcrCod,CogsOcrCo2,CogsOcrCo3,CogsOcrCo4,CogsOcrCo5,PQTReqDate,FromWhsCod,BPLId,WhsName'
        )
            ->where('id', $request["id"])
            ->first();

        //check if document is closed
        //        if (($data->ExtRef && strtolower($data->ExtRef) != "n/a")  || $data->DocStatus == "C") {


        if (!$DocumentTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        if ($ObjType == 205) {
            if (!$request['ReqType']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Request Type is required");
            }

            if (!$request['Requester']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Requester is required");
            }

            if (!$request['ReqDate']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Required date is required");
            }
        }
        if ($data->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("You Cannot Edit the document,Its Closed");
        }

        // if (array_key_exists('BPLId', $request->all()) && empty($request['BPLId']) && $ObjType != 205) {

        //     return (new ApiResponseService())
        //         ->apiFailedResponseService("Branch is Required");
        // }

        // if (array_key_exists('DocDueDate', $request->all()) && empty($request['DocDueDate'] && $ObjType != 205)) {
        //     return (new ApiResponseService())
        //         ->apiFailedResponseService("Delivery Date Required");
        // }

        /**
         * Mapping Req Name
         */
        $ReqName = null;
        if ($ObjType == 205) {
            if ($request['ReqType'] == 12) {
                $ReqName = User::where('id', $request['Requester'])->value('name');
            }

            if ($request['ReqType'] == 171) {
                $employee = OHEM::where('id', $request['Requester'])->first();
                $ReqName = $employee->firstName . " " . $employee->lastName;
            }
        }

        $CardCode =  $request['CardCode'] ??  $data->CardCode;
        $customerDetails = OCRD::where('CardCode', $CardCode)->first();


        DB::connection("tenant")->beginTransaction();
        try {
            $request = $request->all();

            $data->update([
                'SlpCode' => array_key_exists('SlpCode', $request) ? $request['SlpCode'] : $data->SlpCode,
                'NumAtCard' => array_key_exists('NumAtCard', $request) ? $request['NumAtCard'] : $data->NumAtCard,
                'CurSource' => array_key_exists('CurSource', $request) ? $request['CurSource'] : $data->CurSource,
                'DocTotal' => array_key_exists('DocTotal', $request) ? $request['DocTotal'] : $data->DocTotal,
                'VatSum' => array_key_exists('VatSum', $request) ? $request['VatSum'] : $data->VatSum,
                'DocDate' => array_key_exists('DocDate', $request) ? $request['DocDate'] : $data->DocDate,
                'TaxDate' => array_key_exists('TaxDate', $request) ? $request['TaxDate'] : $data->TaxDate,
                'DocDueDate' => array_key_exists('DocDueDate', $request) ? $request['DocDueDate'] : $data->DocDueDate,
                'ReqDate' => array_key_exists('ReqDate', $request) ? $request['ReqDate'] : $data->ReqDate,
                'CntctCode' => array_key_exists('CntctCode', $request) ? $request['CntctCode'] : $data->CntctCode,
                'AgrNo' => array_key_exists('AgrNo', $request) ? $request['AgrNo'] : $data->AgrNo,
                'LicTradNum' => array_key_exists('LicTradNum', $request) ? $request['LicTradNum'] : $data->LicTradNum,
                'BaseEntry' => array_key_exists('BaseEntry', $request) ? $request['BaseEntry'] : $data->BaseEntry,
                'BaseType' => array_key_exists('BaseType', $request) ? $request['BaseType'] : $data->BaseType,
                'UserSign2' => $user->id,
                'Ref2' => array_key_exists('Ref2', $request) ? $request['Ref2'] : $data->Ref2,
                'GroupNum' => array_key_exists('GroupNum', $request) ? $request['GroupNum'] : $data->GroupNum,
                'ToWhsCode' => array_key_exists('ToWhsCode', $request) ? $request['ToWhsCode'] : $data->ToWhsCode,
                'DiscPrcnt' => array_key_exists('DiscPrcnt', $request) ? $request['DiscPrcnt'] : $data->DiscPrcnt,
                'DiscSum' => array_key_exists('DiscSum', $request) ? $request['DiscSum'] : $data->DiscSum,
                'BPLId' => array_key_exists('BPLId', $request) ? $request['BPLId'] : $data->BPLId,
                'Comments' => array_key_exists('Comments', $request) ? $request['Comments'] : $data->Comments,
                'NumAtCard2' => array_key_exists('NumAtCard2', $request) ? $request['NumAtCard2'] : $data->NumAtCard2,
                'JrnlMemo' => array_key_exists('JrnlMemo', $request) ? $request['JrnlMemo'] : $data->JrnlMemo,
                'UseShpdGd' => array_key_exists('UseShpdGd', $request) ? $request['UseShpdGd'] : $data->UseShpdGd,
                'Rounding' => array_key_exists('Rounding', $request) ? $request['Rounding'] : $data->Rounding,
                'RoundDif' => array_key_exists('RoundDif', $request) ? $request['RoundDif'] : $data->RoundDif,
                'Transfered' => "N",
            ]);

            if (isset($request["UserFields"])) {
                $userFields = null;
                foreach ($request["UserFields"] as $key => $field) {
                    $userFields[$key] = $field;
                }

                if (!empty($userFields)) {
                    $data->update($userFields);
                }
            }

            $documentOldLines = $data->document_lines->pluck('id');

            $documentNewLines = collect($request['document_lines'])->pluck('id')->all();


            $difference = $documentOldLines->diff($documentNewLines);

            $ToupdateLines = collect($request['document_lines'])->filter(function ($item) use ($documentOldLines) {
                return isset($item['id']) && $documentOldLines->contains($item['id']);
            })->values()->all();


            foreach ($difference as $id) {
                $DocumentTables->pdi1[0]['ChildTable']::where('id', $id)->delete();
            }

            //Updating Line Details

            $documentRows = [];
            foreach ($ToupdateLines as $key => $value) {
                //$oldLine= $documentOldLines->contains($value['id']);

                $documentOldLinesData = $data->document_lines->where('id', $value['id'])->values();

                $LineNum = $key;
                $ItemCode = null;

                if ($request['DocType'] == "I") {
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
                    // $Dscription = $product->ItemName;

                    // if (!$value['WhsCode']) {
                    //     return (new ApiResponseService())
                    //         ->apiFailedResponseService("Warehouse Required");
                    // }

                    //If Not Sales Order the Inventory Quantities should be Greater

                    // if ($ObjType != 17 && $ObjType != 205 && $ObjType != 14) {
                    //     $inventoryDetails = OITW::where('ItemCode', $ItemCode)
                    //         ->where('WhsCode', $value['WhsCode'])->first();

                    //     if ($product->InvntItem == "Y") {
                    //         if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                    //             return (new ApiResponseService())
                    //                 ->apiFailedResponseService("Insufficient stock for item:" . $value['Dscription']);
                    //         }
                    //     }

                    // }

                    //Serial Number Validations
                    if ($product->ManSerNum == "Y" && $request['ObjType'] != 17) {
                        if ($request['ObjType'] == 14 || $request['ObjType'] == 16) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 15) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }
                    }

                    if (array_key_exists('Quantity', $value) && $value <= 0) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Invalid quantity   for item:" . $value['Dscription']);
                    }

                    if (array_key_exists('Price', $value) && $value['Price'] < 0) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Invalid price for item:" . $value['Dscription']);
                    }
                }

                $DiscPrcn = $value['DiscPrcnt'] ?? 0;
                $rowdetails = [
                    'DocEntry' => $data->id,
                    'OwnerCode' => array_key_exists('OwnerCode', $value) ? $value['OwnerCode'] : $documentOldLinesData[0]->OwnerCode,
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $ItemCode, //    Item ID from OITM AUTO INCREMENT
                    'Dscription' => array_key_exists('Dscription', $value) ? $value['Dscription'] : $documentOldLinesData[0]->Dscription,
                    // 'CodeBars' => array_key_exists('CodeBars', $value) ? $value['CodeBars'] : $documentOldLinesData[0]->CodeBars, //    Bar Code
                    'SerialNum' => array_key_exists('SerialNum', $value) ? $value['SerialNum'] : $documentOldLinesData[0]->SerialNum,
                    //    Serial No.
                    'Quantity' => array_key_exists('Quantity', $value) ? $value['Quantity'] : $documentOldLinesData[0]->Quantity,
                    'DelivrdQty' => array_key_exists('DelivrdQty', $value) ? $value['DelivrdQty'] : $documentOldLinesData[0]->DelivrdQty,
                    'InvQty' => array_key_exists('InvQty', $value) ? $value['InvQty'] : $documentOldLinesData[0]->InvQty,
                    'OpenInvQty' => array_key_exists('OpenInvQty', $value) ? $value['OpenInvQty'] : $documentOldLinesData[0]->OpenInvQty,
                    'PackQty' => array_key_exists('PackQty', $value) ? $value['PackQty'] : $documentOldLinesData[0]->PackQty,
                    'Price' => array_key_exists('Price', $value) ? $value['Price'] : $documentOldLinesData[0]->Price,
                    'DiscPrcnt' => array_key_exists('DiscPrcnt', $value) ? $value['DiscPrcnt'] : $documentOldLinesData[0]->DiscPrcnt,
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : $documentOldLinesData[0]->Rate,
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : $documentOldLinesData[0]->TaxCode,
                    'PriceAfVAT' => array_key_exists('PriceAfVAT', $value) ? $value['PriceAfVAT'] : $documentOldLinesData[0]->PriceAfVAT,
                    'PriceBefDi' => array_key_exists('PriceBefDi', $value) ? $value['PriceBefDi'] : $documentOldLinesData[0]->PriceBefDi,
                    'LineTotal' => array_key_exists('LineTotal', $value) ? $value['LineTotal'] : $documentOldLinesData[0]->LineTotal,
                    'WhsCode' => array_key_exists('WhsCode', $value) ? $value['WhsCode'] : $documentOldLinesData[0]->WhsCode,
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : $documentOldLinesData[0]->ShipDate,
                    'SlpCode' => array_key_exists('SlpCode', $value) ? $value['SlpCode'] : $documentOldLinesData[0]->SlpCode,
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : $documentOldLinesData[0]->Commission,
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : $documentOldLinesData[0]->AcctCode,
                    'OcrCode' => array_key_exists('OcrCode', $value) ? $value['OcrCode'] : $documentOldLinesData[0]->OcrCode,
                    'OcrCode2' => array_key_exists('OcrCode2', $value) ? $value['OcrCode2'] : $documentOldLinesData[0]->OcrCode2,
                    'OcrCode3' => array_key_exists('OcrCode3', $value) ? $value['OcrCode3'] : $documentOldLinesData[0]->OcrCode3,

                    'OcrCode4' => array_key_exists('OcrCode4', $value) ? $value['OcrCode4'] : $documentOldLinesData[0]->OcrCode4,
                    'OcrCode5' => array_key_exists('OcrCode5', $value) ? $value['OcrCode5'] : $documentOldLinesData[0]->OcrCode5,
                    'OpenQty' => array_key_exists('OpenQty', $value) ? $value['OpenQty'] : $documentOldLinesData[0]->OpenQty,

                    'GrossBuyPr' => array_key_exists('GrossBuyPr', $value) ? $value['GrossBuyPr'] : $documentOldLinesData[0]->GrossBuyPr,
                    'GPTtlBasPr' => array_key_exists('GPTtlBasPr', $value) ? $value['GPTtlBasPr'] : $documentOldLinesData[0]->GPTtlBasPr,
                    'BaseType' => array_key_exists('BaseType', $value) ? $value['BaseType'] : $documentOldLinesData[0]->BaseType,
                    'BaseRef' => array_key_exists('BaseRef', $value) ? $value['BaseRef'] : $documentOldLinesData[0]->BaseRef,
                    'BaseLine' => array_key_exists('BaseLine', $value) ? $value['BaseLine'] : $documentOldLinesData[0]->BaseLine,
                    'BaseEntry' => array_key_exists('BaseEntry', $value) ? $value['BaseEntry'] : $documentOldLinesData[0]->BaseEntry,

                    // 'SpecPrice' => array_key_exists('SpecPrice', $value) ? $value['SpecPrice'] : $documentOldLinesData[0]->SpecPrice,
                    'VatSum' => array_key_exists('VatSum', $value) ? $value['VatSum'] : $documentOldLinesData[0]->VatSum,
                    'GrssProfit' => array_key_exists('GrssProfit', $value) ? $value['GrssProfit'] : $documentOldLinesData[0]->GrssProfit,

                    'PoTrgNum' => array_key_exists('PoTrgNum', $value) ? $value['PoTrgNum'] : $documentOldLinesData[0]->PoTrgNum,
                    'OrigItem' => array_key_exists('OrigItem', $value) ? $value['OrigItem'] : $documentOldLinesData[0]->OrigItem,
                    'BackOrdr' => array_key_exists('BackOrdr', $value) ? $value['BackOrdr'] : $documentOldLinesData[0]->BackOrdr,
                    'FreeTxt' => array_key_exists('FreeTxt', $value) ? $value['FreeTxt'] : $documentOldLinesData[0]->FreeTxt,
                    'TrnsCode' => array_key_exists('TrnsCode', $value) ? $value['TrnsCode'] : $documentOldLinesData[0]->TrnsCode,
                    'UomCode' => array_key_exists('UomCode', $value) ? $value['UomCode'] : $documentOldLinesData[0]->UomCode,
                    'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : $documentOldLinesData[0]->unitMsr,
                    'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : $documentOldLinesData[0]->NumPerMsr,
                    'Text' => array_key_exists('Text', $value) ? $value['Text'] : $documentOldLinesData[0]->Text,
                    'GTotal' => array_key_exists('GTotal', $value) ? $value['GTotal'] : $documentOldLinesData[0]->GTotal,
                    'AgrNo' => array_key_exists('AgrNo', $value) ? $value['AgrNo'] : $documentOldLinesData[0]->AgrNo,
                    // 'LinePoPrss' => array_key_exists('LinePoPrss', $value) ? $value['LinePoPrss'] : $documentOldLinesData[0]->LinePoPrss,

                    'CogsOcrCod' => array_key_exists('CogsOcrCod', $value) ? $value['CogsOcrCod'] : $documentOldLinesData[0]->CogsOcrCod,
                    'CogsOcrCo2' => array_key_exists('CogsOcrCo2', $value) ? $value['CogsOcrCo2'] : $documentOldLinesData[0]->CogsOcrCo2,
                    'CogsOcrCo3' => array_key_exists('CogsOcrCo3', $value) ? $value['CogsOcrCo3'] : $documentOldLinesData[0]->CogsOcrCo3,
                    'CogsOcrCo4' => array_key_exists('CogsOcrCo4', $value) ? $value['CogsOcrCo4'] : $documentOldLinesData[0]->CogsOcrCo4,
                    'CogsOcrCo5' => array_key_exists('CogsOcrCo5', $value) ? $value['CogsOcrCo5'] : $documentOldLinesData[0]->CogsOcrCo5,
                    'PQTReqDate' => array_key_exists('PQTReqDate', $value) ? $value['PQTReqDate'] : $documentOldLinesData[0]->PQTReqDate,
                    'FromWhsCod' => array_key_exists('FromWhsCod', $value) ? $value['FromWhsCod'] : $documentOldLinesData[0]->FromWhsCod,
                    'BPLId' => array_key_exists('BPLId', $value) ? $value['BPLId'] : $documentOldLinesData[0]->BPLId,
                    // 'NoInvtryMv' => array_key_exists('NoInvtryMv', $value) ? $value['NoInvtryMv'] : $documentOldLinesData[0]->NoInvtryMv,
                    'WhsName' => array_key_exists('WhsName', $value) ? $value['WhsName'] : $documentOldLinesData[0]->WhsName,
                ];

                $rowItems =  $DocumentTables->pdi1[0]['ChildTable']::where('id', $value['id'])->update($rowdetails);


                /**
                 * Saving Serial Numbers
                 */

                if ($request['DocType'] == "I" && $product->ManSerNum == "Y") {
                    $saveSerialDetails = false;
                    if ($request['ObjType'] == 14 || $request['ObjType'] == 16) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 15) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
                        $saveSerialDetails = true;
                    }

                    if ($saveSerialDetails) {
                        foreach ($value['SerialNumbers'] as $key => $serial) {
                            $LineNum = $key;
                            SRI1::create([
                                "ItemCode" => $ItemCode,
                                "SysSerial" => $serial['SysNumber'] ?? $serial['SysSerial'],
                                "LineNum" => $rowItems->id,
                                "BaseType" => $ObjType,
                                "BaseEntry" => $data->id,
                                "CardCode" => $CardCode,
                                "CardName" => $customerDetails ? $customerDetails->CardName : null,
                                "WhsCode" => $value['WhsCode'],
                                "ItemName" => array_key_exists('Dscription', $value) ? $value['Dscription'] : $documentOldLinesData[0]->Dscription,
                            ]);
                        }
                    }
                }

                array_push($documentRows, $rowItems);
            }

            // Stored Procedure Validations
            //            $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($ObjType, "U", $DocEntry);
            //
            //            if ($storedProcedureResponse) {
            //                if ($storedProcedureResponse->error != 0) {
            //                    return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
            //                }
            //            }

            //Validating Draft using Oringal base type
            if ($ObjType == 112) {
                $mockedDataDraftMessage = (new GeneralDocumentValidationService())->draftValidation($data, $documentRows);
                if ($mockedDataDraftMessage) {
                    return (new ApiResponseService())->apiFailedResponseService($mockedDataDraftMessage);
                }
            }

            /**
             * Compare the Document To BaseDocument
             */
            //   (new GeneralDocumentService())->comporeRowToBaseRow($DocumentTables->ObjectID, $data->id);

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            //            dd($th);
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function updateSingleDocumentOld(Request $request, $ObjType, $DocEntry)
    {
        $user = Auth::user();

        $isDoc = \Request::get('isDoc');

        $ObjType = $ObjType;

        if ($isDoc == 0) {
            $ObjType = 112;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $data = $DocumentTables->ObjectHeaderTable::with('objecttype', 'document_lines')
            ->where('id', $DocEntry)
            ->first();

        //check if document is closed
        if ($data->ExtRef != null || $data->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("You Cannot Edit the document,Its Closed");
        }

        //check if document is transferred
        if ($data->Transfered == "Y") {
            return (new ApiResponseService())
                ->apiFailedResponseService("You Cannot Edit the document, Its Transfered to sap");
        }

        // if (!$request['BPLId'] && $ObjType != 205) {

        //     return (new ApiResponseService())
        //         ->apiFailedResponseService("Branch is Required");
        // }

        if (!$request['DocDueDate'] && $ObjType != 205) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Delivery Date Required");
        }

        if (!$DocumentTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        if ($ObjType == 205) {
            if (!$request['ReqType']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Request Type is required");
            }

            if (!$request['Requester']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Requester is required");
            }

            if (!$request['ReqDate']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Required date is required");
            }
        }

        /**
         * Mapping Req Name
         */
        $ReqName = null;
        if ($ObjType == 205) {
            if ($request['ReqType'] == 12) {
                $ReqName = User::where('id', $request['Requester'])->value('name');
            }

            if ($request['ReqType'] == 171) {
                $employee = OHEM::where('id', $request['Requester'])->first();
                $ReqName = $employee->firstName . " " . $employee->lastName;
            }
        }

        $CardCode = $request['CardCode'];
        $customerDetails = OCRD::where('CardCode', $CardCode)->first();

        $DocNum = (new DocumentsService())
            ->documentNumberingService(
                $request['DocNum'],
                $request['Series']
            );
        DB::connection("tenant")->beginTransaction();
        try {
            $data->update([
                'Series' => $request['Series'],
                'DocNum' => $DocNum,
                'SlpCode' => $request['SlpCode'], // Sales Employee
                'U_SalePipe' => $request['U_SalePipe'], // Sales Pipe Line

                'U_CashName' => $request['U_CashName'], //Cash Customer  Name
                'U_CashNo' => $request['U_CashNo'], // Cash Customer No
                'U_CashMail' => $request['U_CashMail'], // Cash Customer Email
                'U_IDNo' => $request['U_IDNo'], // Id no
                'NumAtCard' => $request['NumAtCard'] ? $request['NumAtCard'] : null,
                'CurSource' => $request['CurSource'],
                'DocTotal' => $request['DocTotal'] ?? 0,
                'VatSum' => $request['VatSum'] ?? 0,
                'DocDate' => $request['DocDate'], //PostingDate
                'TaxDate' => $request['TaxDate'], //Document Date
                'DocDueDate' => $request['DocDueDate'], // Delivery Date
                'ReqDate' => $request['DocDueDate'],
                'CntctCode' => $request['CntctCode'], //Contact Person
                'AgrNo' => $request['AgrNo'],
                'LicTradNum' => $request['LicTradNum'],
                'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //BaseKey
                'BaseType' => $request['BaseType'] ? $request['BaseType'] : null, //BaseKey
                'UserSign2' => $user->id,
                'Ref2' => $request['Ref2'] ? $request['Ref2'] : null, // Ref2
                'GroupNum' => $request['GroupNum'] ? $request['GroupNum'] : null, //[Price List]
                'ToWhsCode' => $request['ToWhsCode'] ? $request['ToWhsCode'] : null, //To Warehouse Code
                'DiscPrcnt' => $request['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' => $request['DiscSum'] ?? 0, // Discount Sum
                'BPLId' => $request['BPLId'],
                'U_SaleType' => $request['U_SaleType'], // Sale Type
                'Comments' => $request['Comments'], //comments
                'NumAtCard2' => $request['NumAtCard2'],
                'JrnlMemo' => $request['JrnlMemo'], // Journal Remarks
                'UseShpdGd' => $request['UseShpdGd'] ?? "N",
                'Rounding' => $request['Rounding'] ?? "N",
                'RoundDif' => $request['RoundDif'] ?? 0,
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
                'Transfered' => "N",
            ]);

            if (isset($request["UserFields"])) {
                $userFields = null;
                foreach ($request["UserFields"] as $key => $field) {
                    $userFields[$key] = $field;
                }
                $data->update($userFields);
            }

            $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $DocEntry)->delete();

            //Updating Line Details

            $documentRows = [];
            foreach ($request['document_lines'] as $key => $value) {
                $LineNum = $key;
                $ItemCode = null;
                $Dscription = $value['Dscription'];

                if ($request['DocType'] == "I") {
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
                    // $Dscription = $product->ItemName;

                    // if (!$value['WhsCode']) {
                    //     return (new ApiResponseService())
                    //         ->apiFailedResponseService("Warehouse Required");
                    // }

                    //If Not Sales Order the Inventory Quantities should be Greater

                    // if ($ObjType != 17 && $ObjType != 205 && $ObjType != 14) {
                    //     $inventoryDetails = OITW::where('ItemCode', $ItemCode)
                    //         ->where('WhsCode', $value['WhsCode'])->first();

                    //     if ($product->InvntItem == "Y") {
                    //         if (!$inventoryDetails || $inventoryDetails->OnHand < $value['Quantity']) {
                    //             return (new ApiResponseService())
                    //                 ->apiFailedResponseService("Insufficient stock for item:" . $value['Dscription']);
                    //         }
                    //     }

                    // }

                    //Serial Number Validations
                    if ($product->ManSerNum == "Y" && $request['ObjType'] != 17) {
                        if ($request['ObjType'] == 14 || $request['ObjType'] == 16) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 15) {
                            if (!isset($value['SerialNumbers']) || $value['Quantity'] != count($value['SerialNumbers'])) {
                                return (new ApiResponseService())
                                    ->apiFailedResponseService("Serial number required  for item:" . $value['Dscription']);
                            }
                        }

                        if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
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
                }

                if (!isset($value['Dscription'])) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Description Required");
                }

                $DiscPrcn = $value['DiscPrcn'] ?? 0;
                $rowdetails = [
                    'DocEntry' => $data->id,
                    'OwnerCode' => $request['OwnerCode'], //Owner Code
                    'LineNum' => $LineNum, //    Row Number
                    'ItemCode' => $ItemCode, //    Item ID from OITM AUTO INCREMENT
                    'Dscription' => $Dscription, // Item Description
                    'CodeBars' => $value['CodeBars'], //    Bar Code
                    'SerialNum' => $value['SerialNum'], //    Serial No.
                    'Quantity' => $value['Quantity'] ?? 1, //    Quantity
                    'DelivrdQty' => $value['DelivrdQty'], //    Delivered Qty
                    'InvQty' => $value['InvQty'], //   Qty(Inventory UoM)
                    'OpenInvQty' => $value['OpenInvQty'], //Open Inv. Qty ------
                    'PackQty' => $value['PackQty'], //    No. of Packages
                    'Price' => $DiscPrcn == 0 ? $request['Price'] : $value['PriceBefDi'], //    Price After Discount
                    'DiscPrcnt' => array_key_exists('DiscPrcnt', $value) ? $value['DiscPrcnt'] : 0, //    Discount %
                    'Rate' => array_key_exists('Rate', $value) ? $value['Rate'] : null, //    Rate
                    'TaxCode' => array_key_exists('TaxCode', $value) ? $value['TaxCode'] : null, //    Tax Code
                    'PriceAfVAT' => array_key_exists('PriceAfVAT', $value) ? $value['PriceAfVAT'] : 0, //       Gross Price after Discount
                    'PriceBefDi' => array_key_exists('PriceBefDi', $value) ? $value['PriceBefDi'] : 0, // Unit Price
                    'LineTotal' => $value['LineTotal'] ?? $value['Price'], //    Total (LC)
                    'WhsCode' => $value['WhsCode'] ?? null, //    Warehouse Code
                    'ShipDate' => array_key_exists('ShipDate', $value) ? $value['ShipDate'] : null, //    Del. Date
                    'SlpCode' => $request['SlpCode'], //    Sales Employee
                    'Commission' => array_key_exists('Commission', $value) ? $value['Commission'] : null, //    Comm. %
                    'AcctCode' => array_key_exists('AcctCode', $value) ? $value['AcctCode'] : null, //    G/L Account
                    'OcrCode' => $value['OcrCode'], //    Dimension 1
                    'OcrCode2' => $value['OcrCode2'], //    Dimension 2
                    'OcrCode3' => $value['OcrCode3'], //    Dimension 3
                    'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                    'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                    'OpenQty' => array_key_exists('Quantity', $value) ? $value['Quantity'] : null, //    Open Inv. Qty
                    'GrossBuyPr' => array_key_exists('GrossBuyPr', $value) ? $value['GrossBuyPr'] : null, //   Gross Profit Base Price
                    'GPTtlBasPr' => array_key_exists('GPTtlBasPr', $value) ? $value['GPTtlBasPr'] : null, //    Gross Profit Total Base Price

                    'BaseType' => $request['BaseType'] ?? null, //    Base Type
                    'BaseRef' => $request['BaseRef'] ?? null, //    Base Ref.
                    'BaseEntry' => $request['BaseEntry'] ?? null, //    Base Key
                    'BaseLine' => $value['BaseLine'] ?? null, //    Base Row

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
                    'OwnerCode' => $value['OwnerCode'] ?? null, //    Owner
                    'GTotal' => array_key_exists('GTotal', $value) ? $value['GTotal'] : null, //    Gross Total
                    'AgrNo' => array_key_exists('AgrNo', $value) ? $value['AgrNo'] : null, //    Blanket Agreement No.
                    'LinePoPrss' => array_key_exists('LinePoPrss', $value) ? $value['LinePoPrss'] : null, //    Allow Procmnt. Doc.

                    //Cogs Values
                    'CogsOcrCod' => $value['OcrCode'],
                    'CogsOcrCo2' => $value['OcrCode2'],
                    'CogsOcrCo3' => $value['OcrCode3'],
                    'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                    'CogsOcrCo5' => $value['OcrCode5'] ?? null,
                    //Inventory Transaction  Value
                    'PQTReqDate' => $request['ReqDate'],
                    'FromWhsCod' => $value['FromWhsCod'] ?? null, // // From Warehouse Code
                    'BPLId' => $request['BPLId'],
                    'U_StockWhse' => isset($value['U_StockWhse']) ? $value['U_StockWhse'] : null,
                    'NoInvtryMv' => $value['NoInvtryMv'] ?? "N",
                    'U_Promotion' => $value['U_Promotion'] ?? 'Charged',
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,

                ];

                $rowItems = new $DocumentTables->pdi1[0]['ChildTable']($rowdetails);
                $rowItems->save();

                /**
                 * Saving Serial Numbers
                 */

                if ($request['DocType'] == "I" && $product->ManSerNum == "Y") {
                    $saveSerialDetails = false;
                    if ($request['ObjType'] == 14 || $request['ObjType'] == 16) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 15) {
                        $saveSerialDetails = true;
                    }
                    if ($request['ObjType'] == 13 && $request['BaseType'] != 15) {
                        $saveSerialDetails = true;
                    }

                    if ($saveSerialDetails) {
                        foreach ($value['SerialNumbers'] as $key => $serial) {
                            $LineNum = $key;
                            SRI1::create([
                                "ItemCode" => $ItemCode,
                                "SysSerial" => $serial['SysNumber'] ?? $serial['SysSerial'],
                                "LineNum" => $rowItems->id,
                                "BaseType" => $ObjType,
                                "BaseEntry" => $data->id,
                                "CardCode" => $CardCode,
                                "CardName" => $customerDetails ? $customerDetails->CardName : null,
                                "WhsCode" => $value['WhsCode'],
                                "ItemName" => $Dscription,
                            ]);
                        }
                    }
                }

                array_push($documentRows, $rowItems);
            }

            // Stored Procedure Validations
            $storedProcedureResponse = (new DatabaseValidationServices())->validateTransactions($ObjType, "U", $DocEntry);

            if ($storedProcedureResponse) {
                if ($storedProcedureResponse->error != 0) {
                    return (new ApiResponseService())->apiFailedResponseService($storedProcedureResponse->error_message);
                }
            }

            //Validating Draft using Oringal base type
            if ($ObjType == 112) {
                $mockedDataDraftMessage = (new GeneralDocumentValidationService())->draftValidation($data, $documentRows);
                if ($mockedDataDraftMessage) {
                    return (new ApiResponseService())->apiFailedResponseService($mockedDataDraftMessage);
                }
            }

            /**
             * Compare the Document To BaseDocument
             */
            //   (new GeneralDocumentService())->comporeRowToBaseRow($DocumentTables->ObjectID, $data->id);

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Closing Document
     */
    public function closeSingleDocument(Request $request, $ObjType, $DocEntry)
    {
        $user = Auth::user();
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'update');
        try {
            $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)->first();
            if (!$data) {
                return (new ApiResponseService())->apiFailedResponseService("Document Does not Exist");
            }

            $data->update([
                'DocStatus' => "C",
            ]);

            return (new ApiResponseService())->apiSuccessResponseService("Closed Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Mark the Document Printed
     */

    public function markDocumentPrinted(Request $request, $ObjType, $DocEntry)
    {
        $user = Auth::user();
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        //        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'update');
        try {
            $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)->first();
            if (!$data) {
                return (new ApiResponseService())->apiFailedResponseService("Document Does not Exist");
            }
            $data->update([
                'Printed' => "Y",
            ]);

            return (new ApiResponseService())->apiSuccessResponseService("Closed Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Document Approvers
     */

    /**
     * Mark the Document Printed
     */

    public function getDocumentApprovalStatus(Request $request, $ObjType, $DocEntry)
    {
        try {
            $isDoc = \Request::get('isDoc');
            $isForPrint = \Request::get('isForPrint');

            $ObjType = $ObjType;

            $originalObjType = $ObjType;

            if ($isDoc == 0) {
                $ObjType = 112;
            }

            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', $ObjType)
                ->first();

            //            (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'view');

            $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)
                ->first();

            $generalObjects = [205, 66, 13, 17];

            if (!in_array($ObjType, $generalObjects)) {

                $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)
                    ->first();
            }

            $data->originIsApproval = 0;

            $owdd = OWDD::where('DraftEntry', $data->ExtRef)->first();

            if ($owdd) {

                $data->originIsApproval = 1;

                $approvers = WDD1::where('WddCode', $owdd->WddCode)->get();

                foreach ($approvers as $key => $approver) {

                    $approver->ApprovalTime = "";
                    $approver->StatusRemarks = $approver->Remarks;

                    if ($approver->Status == "N") {
                        $approver->StatusComment = "Rejected";
                        $approver->ApprovalTime = $approver->updated_at;
                    }

                    if ($approver->Status == "W") {
                        $approver->StatusComment = "Pending";
                    }

                    if ($approver->Status == "Y") {
                        $approver->StatusComment = "Approved";
                        $approver->ApprovalTime = $approver->updated_at;
                    }

                    $userDetails = User::where('ExtRef', $approver->UserID)->first();
                    $approver->userDetails = $userDetails;

                    if ($userDetails) {
                        $approver->Date = now()->format('Y-m-d');
                        $approver->imagePath = $userDetails ? "data:image/jpeg;base64," . $userDetails->signaturePath : null;
                    }
                }

                $data->approvers = $approvers;

                $data->owdd = $owdd;
            }
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Customer Documents
     */

    public function getCustomerDocData($ObjType)
    {
        try {
            $CardCode = \Request::get('cardCode');

            $DocumentTables = APDI::where('ObjectID', $ObjType)
                ->first();
            $data = $DocumentTables->ObjectHeaderTable::where('ObjType', $ObjType)
                ->where('CardCode', $CardCode)
                ->orderBy('id', 'desc')
                ->take(100)
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
