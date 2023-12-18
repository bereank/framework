<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Banking\Models\PDF2;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Actions\MapApiFieldAction;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\MarketingDocuments\Models\ATC1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\MarketingDocuments\Services\MarketingDocumentService;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentService;




class MarketingDocumentsController extends Controller
{
    public function getDocumentData(Request $request, $ObjType)
    {

        $isDoc = \Request::get('isDoc');
        $docNum = \Request::get('docNum');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 50);

        $StartDate = request()->filled('StartDate') ? Carbon::parse(request()->input('StartDate'))->startOfDay() : Carbon::now()->startOfMonth();

        $EndDate = request()->filled('EndDate') ? Carbon::parse(request()->input('EndDate'))->endOfDay() : Carbon::now()->endOfMonth();

        $user = User::where('id', Auth::user()->id)->with('oudg')->first();

        $tableObjType = $ObjType;
        if ($isDoc == 0) {
            $tableObjType = 112;
        }


        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $tableObjType)
            ->first();
        //    (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'read');
        $ownerData = [];
        $dataOwnership = (new AuthorizationService())->CheckIfActive($ObjType, $user->EmpID);

        if ($dataOwnership) {
            $ownerData =  (new AuthorizationService())->getDataOwnershipAuth($ObjType, 1);
        }

        try {
            $data = $DocumentTables->ObjectHeaderTable::where('ObjType', $ObjType)
                ->with('CreatedBy.ohem')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            if ($ObjType != 205) {
                $data = $DocumentTables->ObjectHeaderTable::select('id', 'CardCode', 'DocNum', 'CardName', 'ExtRef', 'ExtRefDocNum', 'UserSign', 'SlpCode', 'DataSource', 'DocStatus', 'ObjType', 'OwnerCode', 'DocDate', 'DocTotal', 'created_at')
                    ->where('ObjType', $ObjType)
                    ->when($dataOwnership && $dataOwnership->Active, function ($query) use ($ownerData) {
                        $query->wherein('OwnerCode', $ownerData);
                    })
                    ->with('CreatedBy:name', 'ohem:id,empID,firstName,middleName,lastName')
                    ->with(['document_lines' => function ($query) {
                        $query->with('ItemDetails:id,ItemCode,ItemName')
                            ->select('id', 'DocEntry', 'Quantity', 'Price', 'LineTotal', 'ItemCode');
                    }])
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

                    ->where(function ($q) use ($docNum) {
                        if ($docNum) {
                            $q->where('DocNum', $docNum)->orWhere('ExtRefDocNum', $docNum);
                        }
                    })
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
    public function getSingleDocData($ObjType, $DocEntry)
    {
        $isDoc  = request()->filled('isDoc') ? request()->input('isDoc') : 1;

        $isForPrint = \Request::get('isForPrint');
        $copyTo = \Request::get('copyTo');

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

        $data = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)
            ->with("document_lines.oitm")
            ->first();



        $rows = $data->document_lines;
        $generalObjects = [205, 66];
        if (!in_array($ObjType, $generalObjects)) {
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
        return $data;
    }

    /**
     * Store a new marketing document based on the provided request.
     *
     * This method validates the request data, performs defaulting of fields, 
     * validates the document fields, and creates a new marketing document.

     * @param \Illuminate\Http\Request $request
     *
     */
    public function store(Request $request)
    {
        // Step 1: Object Validation
        $validator = Validator::make($request->all(), [
            'ObjType' => 'required',
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }

        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $request['ObjType'])
            ->first();

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $request['ObjType']);
        }

        // Step 2: Default Fields
        $defaulted_data = (new MarketingDocumentService())->fieldsDefaulting($request->all());

        // Step 3: Validate Document Fields
        $validatedFields  = (new MarketingDocumentService())->validateFields($defaulted_data, $request['ObjType']);

        // Step 4: Validate UDF'S
        $docData = (new MapApiFieldAction())->handle($validatedFields, $TargetTables);

        // Step 5: Create Document
        $newDoc =  (new MarketingDocumentService())->createDoc($docData, $TargetTables, $request['ObjType']);

        //step 6: Record Payment data
        if ($request['payments']) {
            foreach ($request['payments'] as $payment) {
//                $storedProcedureResponse = null;
                if ($newDoc["ObjType"] == 13) {
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
        return (new ApiResponseService())->apiSuccessResponseService($newDoc);
    }
}
