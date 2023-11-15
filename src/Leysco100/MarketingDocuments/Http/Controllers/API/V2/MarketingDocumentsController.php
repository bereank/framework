<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Actions\MapApiFieldAction;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\MarketingDocuments\Services\MarketingDocumentService;




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
        // Step 4: Create Document
        $docData = (new MapApiFieldAction())->handle($validatedFields, $TargetTables);

        // Step 5: Create Document
        return (new MarketingDocumentService())->createDoc($docData, $TargetTables, $request['ObjType']);
    }
}
