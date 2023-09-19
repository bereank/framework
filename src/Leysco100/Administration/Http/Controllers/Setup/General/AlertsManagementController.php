<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ALT1;
use Leysco100\Shared\Models\Administration\Models\OALT;
use Leysco100\Administration\Http\Controllers\Controller;

class AlertsManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $alerts_data = OALT::with('alt1')->get();
            return (new ApiResponseService())->apiSuccessResponseService($alerts_data);
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
            $validatedData = $request->validate([
                'Code' => 'nullable|string',
                'Name' => 'required|string',
                'Type' => 'required|string',
                'Priority' => 'required|integer',
                'Active' => 'required|boolean',
            ]);
            $data =   OALT::create([
                'Code' =>  $request['Code'],
                'Name' => $request['Name'],
                'Type' => $request['Type'],
                'Priority' => $request['Priority'] ?? null,
                'Active' => $request['Active'] ?? null,
                'NumOfParam' => $request['NumOfParam'] ?? null,
                'ParamData' =>  $request['ParamData'] ?? null,
                'Params' =>  $request['Params'] ?? null,
                'NumOfDocs' => $request['NumOfDocs'] ?? null,
                'DocsData' =>  $request['DocsData'] ?? null,
                'Docs' =>  $request['Docs'] ?? null,
                'UserText' =>  $request['UserText'] ?? null,
                'QueryId' => $request['QueryId'] ?? null,
                'FrqncyType' =>  $request['FrqncyType'] ?? null,
                'FrqncyIntr' =>  $request['FrqncyIntr'] ?? null,
                'ExecDaY' =>  $request['ExecDaY'] ?? null,
                'ExecTime' =>  $request['ExecTime'] ?? null,
                'LastDate' =>  $request['LastDate'] ?? null,
                'LastTIME' =>  $request['LastTIME'] ?? null,
                'NextDate' =>  $request['NextDate'] ?? null,
                'NextTime' =>  $request['NextTime'] ?? null,
                'UserSign' => Auth::user()->id,
                'History' =>  $request['History'] ?? null,
                'QCategory' =>  $request['QCategory'] ?? null,
            ]);
            foreach ($request['alt1'] as $user) {
                ALT1::create([
                    'DocEntry' => $data->id,
                    'Code' =>  $request['Code'] ?? null,
                    'SendIntrnl' =>  $user['SendIntrnl'] ?? false,
                    'SendEMail' =>  $user['SendEMail'] ?? false,
                    'SendSMS' =>  $user['SendSMS'] ?? false,
                    'SendFax' =>  $user['SendFax'] ?? false,
                    'UserSign' =>  $user['id']
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully !!");
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $alerts_data = OALT::with('alt1.users')->where('id', $id)->first();


            return (new ApiResponseService())->apiSuccessResponseService($alerts_data);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
            $validatedData = $request->validate([
                'Code' => 'nullable|string',
                'Name' => 'required|string',
                'Type' => 'required|string',
                'Priority' => 'required|integer',
                'Active' => 'required|boolean',
                'alt1' => 'required|array'
            ]);
            $data =   OALT::where('id', $id)->update([
                'Code' =>  $request['Code'],
                'Name' => $request['Name'],
                'Type' => $request['Type'],
                'Priority' => $request['Priority'] ?? null,
                'Active' => $request['Active'] ?? null,
                'NumOfParam' => $request['NumOfParam'] ?? null,
                'ParamData' =>  $request['ParamData'] ?? null,
                'Params' =>  $request['Params'] ?? null,
                'NumOfDocs' => $request['NumOfDocs'] ?? null,
                'DocsData' =>  $request['DocsData'] ?? null,
                'Docs' =>  $request['Docs'] ?? null,
                'UserText' =>  $request['UserText'] ?? null,
                'QueryId' => $request['QueryId'] ?? null,
                'FrqncyType' =>  $request['FrqncyType'] ?? null,
                'FrqncyIntr' =>  $request['FrqncyIntr'] ?? null,
                'ExecDaY' =>  $request['ExecDaY'] ?? null,
                'ExecTime' =>  $request['ExecTime'] ?? null,
                'LastDate' =>  $request['LastDate'] ?? null,
                'LastTIME' =>  $request['LastTIME'] ?? null,
                'NextDate' =>  $request['NextDate'] ?? null,
                'NextTime' =>  $request['NextTime'] ?? null,
                'UserSign' => Auth::user()->id,
                'History' =>  $request['History'] ?? null,
                'QCategory' =>  $request['QCategory'] ?? null,
            ]);

            $existing = ALT1::where('DocEntry', $id)->pluck('UserSign');
            $updated = collect($request['alt1'])->pluck('UserSign');
            $removed = $existing->diff($updated);

            if (array_key_exists('alt1',  $validatedData)) {
                foreach ($request['alt1'] as $user) {
                    $user_exists =     ALT1::where('UserSign', $user['UserSign'])->first();
                    if ($user_exists) {
                        ALT1::where('UserSign', $user['UserSign'])->update(
                            [
                                'DocEntry' => $id,
                                'UserSign' =>  $user['UserSign'],
                                'Code' =>  $request['Code'] ?? null,
                                'SendIntrnl' =>  $user['SendIntrnl'] ?? false,
                                'SendEMail' =>  $user['SendEMail'] ?? false,
                                'SendSMS' =>  $user['SendSMS'] ?? false,
                                'SendFax' =>  $user['SendFax'] ?? false,
                            ]
                        );
                    } else {
                        ALT1::create(
                            [
                                'DocEntry' => $id,
                                'UserSign' =>  $user['UserSign'],
                                'Code' =>  $request['Code'] ?? null,
                                'SendIntrnl' =>  $user['SendIntrnl'] ?? false,
                                'SendEMail' =>  $user['SendEMail'] ?? false,
                                'SendSMS' =>  $user['SendSMS'] ?? false,
                                'SendFax' =>  $user['SendFax'] ?? false,
                            ]
                        );
                    }
                }
                foreach ($removed as $remvd) {
                    ALT1::where('UserSign', $remvd)->delete();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully !!");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}