<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\DocumentsService;
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
        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        $tableObjType = $ObjType;
        if ($isDoc == 0) {
            $tableObjType = 112;
        }
        $StartDate = $request['StartDate'];
        $EndDate = $request['EndDate'];
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
                    ->when($dataOwnership->Active, function ($query) use ($ownerData) {
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
    public function store(Request $request)
    {


        $data = (new MarketingDocumentService())->BasicValidation($request);


        $ObjType = (int) $request['ObjType'];

        $data['saveToDraft'] = false;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$TargetTables) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Not found document with objtype " . $ObjType);
        }

        /**
         * Handling  Document Numbering
         */

        $series = (new DocumentsService())->gettingNumberingSeries($TargetTables->id);

        $data['DocNum'] =  $series['DocNum'];
        $data['Series'] = $series['Series'];
        /**
         * Check if The Item has External Approval
         */
        if ($TargetTables->hasExtApproval == 1) {
            $data['saveToDraft'] = true;
            $TargetTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }

        /**
         * Check If Authorized
         */
        //        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'write');

        /**
         * Mapping Req Name
         */

        $data['ReqName'] = null;
        if ($ObjType == 205) {
            if ($request['ReqType'] == 12) {
                $data['ReqName'] = User::where('id', $request['Requester'])->value('name');
            }

            if ($request['ReqType'] == 171) {
                $employee = OHEM::where('id', $request['Requester'])->first();
                $data['ReqName'] = $employee->firstName . " " . $employee->lastName;
            }
        }

        $data['checkStockAvailabilty'] = false;

        if (($ObjType == 13 && $request['BaseType'] != 15) || $ObjType == 15) {
            $data['checkStockAvailabilty']  = true;
        }

        // DB::connection("tenant")->beginTransaction();
        // try {


        $docData = (new MarketingDocumentService())->createDoc($data, $TargetTables);
    }
}
