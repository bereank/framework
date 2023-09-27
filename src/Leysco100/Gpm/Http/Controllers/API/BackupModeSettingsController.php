<?php


namespace Leysco100\Gpm\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\MarketingDocuments\Models\AutoBCModeSettings;

class BackupModeSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $ObjType = 216;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = AutoBCModeSettings::first();
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

        $ObjType = 216;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'create');
        try {
            $request->validate([
                // 'UserSign' => 'nullable|exists:users,id',
                'UserSign' => 'nullable',
                'Status' => 'required',
                'DoesNotExistCount' => 'nullable|integer',
                'LastSyncDuration' => 'nullable|integer',
                'DurationType' => 'required|in:hours,minutes,seconds',
                //'FieldsTemplate' => 'required|exists:form_fields_templates,id',
                'FieldsTemplate' => 'required',
                'ActiveFrom' => "required",
                "ActiveTo" => "required",
                "isDistinctDocs" => 'required',
                "NotifyAfter" => 'required',
                "NotifyType" => 'required'
            ]);

            AutoBCModeSettings::updateOrCreate(
                ['id' => 1],
                [
                    'UserSign' => Auth::user()->id,
                    'LastSyncDuration' => $request['LastSyncDuration'],
                    'DoesNotExistCount' => $request['DoesNotExistCount'],
                    'FieldsTemplate' => $request['FieldsTemplate'],
                    'DurationType' => $request['DurationType'],
                    'Status' => $request['Status'],
                    'ActiveFrom' => $request['ActiveFrom'],
                    "ActiveTo" => $request['ActiveTo'],
                    "isDistinctDocs" => $request["isDistinctDocs"],
                    "NotifyType" => $request['NotifyType'],
                    "NotifyAfter" => $request['NotifyAfter']
                ]
            );

            return (new ApiResponseService())->apiSuccessResponseService('Saved Successfully !!!');
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
