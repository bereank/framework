<?php

namespace Leysco100\Administration\Http\Controllers\SystemInit\GeneralSettings;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Administration\Http\Controllers\Controller;


class GeneralSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = OADM::where('id', 1)->first();
        $userDefaults = Auth::user()->oudg;
        $data->DefaultBPLId = $userDefaults ? $userDefaults->BPLId : null; // Default Branch
        return (new ApiResponseService())->apiSuccessResponseService($data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {

            $company = OADM::where('id', 1)->first();

            $generalSettings = [
                'DfltWhs' => $request['DfltWhs'], // Default Warehouse
                'GLMethod' => $request['GLMethod'], // G/L Method
                'DftItmsGrpCod' => $request['DftItmsGrpCod'], // Defautl Item Group
                'copyToUnsyncDocs' => $request['copyToUnsyncDocs'] == true ? 1 : 0,
                'printUnsyncDocs' => $request['printUnsyncDocs'] == true ? 1 : 0,
                'SPEnabled' => $request['SPEnabled'] == true ? 1 : 0,
                'SPAOffline' => $request['SPAOffline'] == true ? 1 : 0,
                'useLocalSearch' => $request['useLocalSearch'] == true ? 1 : 0,
                'NotifAlert' => $request['NotifAlert'],
                'NotifEmail' => $request['NotifEmail'],
                'localUrl' => $request['localUrl'],
            ];

            $company->update($generalSettings);


            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    
    public function updatePswdChangOnReset(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'PswdChangeOnReset' => 'nullable',
                'HasOtpVerification' => 'nullable'
            ]);

            $PswdChange = OADM::findOrFail(1);
            $PswdChange->PswdChangeOnReset = $validatedData['PswdChangeOnReset'];
            $PswdChange->HasOtpVerification = $validatedData['HasOtpVerification'];
            $PswdChange->save();

            return (new ApiResponseService())->apiSuccessResponseService($request);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}