<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V2;

use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Banking\Models\PDF2;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Services\UserFieldsService;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Actions\MapApiFieldAction;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\MarketingDocuments\Models\ATC1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\MarketingDocuments\Services\MarketingDocumentService;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\MarketingDocuments\Services\DatabaseValidationServices;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentService;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentValidationService;




class MarketingDocumentsController extends Controller
{
    public function getDocumentData(Request $request, $ObjType)
    {

        // Query params validation rules
        $rules = [
            'StartDate' => 'nullable|date_format:Y-m-d',
            'EndDate' => 'nullable|date_format:Y-m-d',
            'per_page' => 'nullable|integer',
            'isDoc' => 'nullable|integer',
            'page' => 'nullable|integer'
        ];

        // Validate the request
        $validator = Validator::make($request->query(), $rules);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiValidationFailedResponse($validator->errors());
        }

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
        (new AuthorizationService())->checkIfAuthorize($DocumentTables->id, 'read');
        $ownerData = [];
        $dataOwnership = (new AuthorizationService())->CheckIfActive($ObjType, $user->EmpID);

        if ($dataOwnership) {
            $ownerData =  (new AuthorizationService())->getDataOwnershipAuth($ObjType, 1);
        }



        try {

            if ($ObjType != 205) {

                $DocumentTables['doctype'] = $ObjType;
                $record = (new UserFieldsService())->processUDF($DocumentTables);
                $udf = [];
                if ($record) {
                    foreach ($record['HeaderUserFields'] as $headerField) {
                        $udf[] =    $headerField['FieldName'];
                    }
                }
                $data = $DocumentTables->ObjectHeaderTable::select(
                    'id',
                    'CardCode',
                    'DocNum',
                    'CardName',
                    'NumAtCard',
                    'ExtRef',
                    'ExtRefDocNum',
                    'UserSign',
                    'Series',
                    'SlpCode',
                    'DataSource',
                    'DocStatus',
                    'ObjType',
                    'OwnerCode',
                    'DocDate',
                    'DocTotal',
                    'created_at',
                    ...$udf
                )
                    ->where('ObjType', $ObjType)
                    ->when($dataOwnership && $dataOwnership->Active, function ($query) use ($ownerData) {
                        $query->wherein('OwnerCode', $ownerData);
                    })
                    ->with('CreatedBy:name', 'ofscs.fsc1', 'ohem:id,empID,firstName,middleName,lastName')
                    ->with(['document_lines' => function ($query) {
                        $query->with('ItemDetails:id,ItemCode,ItemName')
                            ->select('id', 'DocEntry', 'LineNum', 'Quantity', 'Price', 'LineTotal', 'ItemCode', 'Dscription');
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

                $DocumentTables['doctype'] = $ObjType;
                $udf = [];

                foreach ($data as $key => $singleRcd) {
                    if ($singleRcd->count() > 0) {
                        $record = (new UserFieldsService())->processUDF($DocumentTables);

                        $userFields = [];

                        if ($record) {
                            foreach ($record['HeaderUserFields'] as $headerField) {
                                $udf[] = $headerField['FieldName'];
                                $userField = new stdClass();
                                $userField->{$headerField['FieldName']} = $singleRcd->{$headerField['FieldName']};
                                $userFields[] = $userField;
                            }

                            $singleRcd->UserFields = $userFields;
                        }
                    }
                }
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
        Log::info($request);
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

        $ObjType = $request['ObjType'];

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $request['ObjType']);
        }

        //        if ($TargetTables->hasExtApproval == 1) {
        //            $TargetTables = APDI::with('pdi1')
        //                ->where('ObjectID', 112)
        //                ->first();
        //            $ObjType = 112;
        //        }

        // Step 2: Default Fields
        $defaulted_data = (new MarketingDocumentService())->fieldsDefaulting($request->all());
        // return  $defaulted_data;
        // Step 3: Validate Document Fields
        $validatedFields  = (new MarketingDocumentService())->validateFields($defaulted_data, $request['ObjType']);

        // Step 4: Validate UDF'S
        $docData = (new MapApiFieldAction())->handle($validatedFields, $TargetTables);

        if ($docData["status"] == -1) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($docData['data']);
        } else {
            $docData = $docData['data'];
        }
        // Step 5: Create Document
        $newDoc =  (new MarketingDocumentService())->createDoc($docData, $TargetTables, $ObjType);


        return (new ApiResponseService())->apiSuccessResponseService($newDoc);
    }

    //UpdateDocument
    public function updateSingleDocument(Request $request)
    {
        $isDoc = \Request::get('isDoc');
        $ObjType = $request["ObjType"];

        if ($isDoc == 0) {
            $ObjType = 112;
        } else {
            $isDoc = 1;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $Headerdata = $DocumentTables->ObjectHeaderTable::where('id', $request["id"])
            ->first();
        if (!$Headerdata) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Specified Document Not Found");
        }
        //check if document is closed
        if ($Headerdata->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("You Cannot Edit the document,Its Closed");
        }

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
        // Step 1: Default Fields
        $defaulted_data = (new MarketingDocumentService())->fieldsDefaulting($request->all());

        // Step 2: Validate Document Fields
        //   $validatedFields  = (new MarketingDocumentService())->validateFields($defaulted_data, $request['ObjType']);

        // Step 3: Validate UDF'S
        $docData = (new MapApiFieldAction())->handle($defaulted_data, $DocumentTables);
        // Update  Document

        $newDoc =  (new MarketingDocumentService())->updateDoc($docData, $ObjType, $Headerdata);

        return (new ApiResponseService())->apiSuccessResponseService($newDoc);
    }

    public function documentCancellation($ObjType, $id)
    {
        $DocTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$DocTables) {
            return (new ApiResponseService())->apiFailedResponseService("Not found document with base type ");
        }
        /**
         * Check If Authorized
         */
        //        (new AuthorizationService())->checkIfAuthorize($DocTables->id, 'update');


        $DocHeader = $DocTables->ObjectHeaderTable::where('id', $id)
            ->with('document_lines')
            ->first();
        if (!$DocHeader) {
            return (new ApiResponseService())->apiFailedResponseService("Document Not Found");
        }

        if ($DocHeader->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document is closed");
        }
        if ($DocHeader->CANCELED == "Y") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document already cancelled");
        }
        if ($DocHeader->CANCELED == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible on a Cancellation Document");
        }
        $DocLines = $DocTables->pdi1[0]['ChildTable']::where('DocEntry', $id)
            ->where(function ($query) {
                $query
                    //->orWhereColumn('OpenQty', '!=', 'Quantity')
                    ->orWhere('LineStatus', '!=', 'O');
            })
            ->exists();
        if (!$DocLines) {
            try {

                DB::connection("tenant")->beginTransaction();
                $Numbering = (new DocumentsService())
                    ->getNumSerieByObjectId($ObjType);
                $cancellation_header =  new $DocTables->ObjectHeaderTable($DocHeader->toArray());
                $cancellation_header->CANCELED = 'C';
                $cancellation_header->DocStatus = 'C';
                $cancellation_header->DocNum = $Numbering['NextNumber'];
                $cancellation_header->BaseType = $DocHeader->ObjType;
                $cancellation_header->BaseEntry = $DocHeader->id;
                $cancellation_header->OwnerCode =   Auth::user()->EmpID ?? "";
                $cancellation_header->save();
                //Close Current document
                $DocTables->ObjectHeaderTable::where('id', $DocHeader->id)
                    ->update([
                        'DocStatus' => 'C'
                    ]);
                foreach ($DocHeader->document_lines as $key => $line) {

                    $LineNum = ++$key;
                    $cancellation_line = new  $DocTables->pdi1[0]['ChildTable']($line->toArray());
                    $cancellation_line->DocEntry = $cancellation_header->id;
                    $cancellation_line->BaseType = $DocHeader->ObjType;
                    $cancellation_line->BaseEntry = $DocHeader->id;
                    $cancellation_line->BaseLine = $line->LineNum;
                    $cancellation_line->LineNum =  $LineNum;
                    $cancellation_line->LineStatus = "C";
                    $cancellation_line->save();
                    $BaseTables = APDI::with('pdi1')
                        ->where('ObjectID', $line->BaseType)
                        ->first();
                    //Open base document header
                    $baseHeader =   $BaseTables->ObjectHeaderTable::where('id', $line->BaseEntry)
                        ->update([
                            'DocStatus' => "O"
                        ]);


                    $baseLine = $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $line->BaseEntry)
                        ->where('LineNum', $line->BaseLine)->first();
                    //Open base line
                    $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $line->BaseEntry)
                        ->where('LineNum', $line->BaseLine)
                        ->update([
                            'LineStatus' => "O",
                            'OpenQty' => $line->OpenQty + $baseLine->OpenQty,
                        ]);
                    Log::info([$line->OpenQty, "BASE" => $baseLine->OpenQty]);
                    //Close current line
                    $DocTables->pdi1[0]['ChildTable']::where('id', $line->id)
                        ->update([
                            'LineStatus' => "C",
                        ]);
                }
                (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
                DB::connection("tenant")->commit();
                return (new ApiResponseService())->apiSuccessResponseService(['message' =>
                "Successfully Canceled"]);
            } catch (\Throwable $th) {
                DB::connection("tenant")->rollback();
                return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
            }
        } else {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document Lines Not Open");
        }
    }
}
