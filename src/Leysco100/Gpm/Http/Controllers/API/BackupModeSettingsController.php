<?php


namespace Leysco100\Gpm\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
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
        try {
            $request->validate([
                'UserSign' => 'nullable|exists:users,id',
                'Status' => 'required|integer|in:0,1',
                'DoesNotExistCount' => 'nullable|integer',
                'LastSyncDuration' => 'nullable|integer',
                'DurationType' => 'required|in:hours,minutes,seconds',
                'FieldsTemplate' => 'required|exists:form_fields_templates,id',
                'ActiveFrom' => "required",
                "ActiveTo" => "required",
                "isDistinctDocs" => 'required'
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
                    "isDistinctDocs" => $request["isDistinctDocs"]

                ]
            );

            return (new ApiResponseService())->apiSuccessResponseService('Saved Successfully !!!');
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
