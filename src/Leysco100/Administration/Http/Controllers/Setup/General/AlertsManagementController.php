<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;

use Illuminate\Http\Request;
use Leysco100\Gpm\Mail\AlertMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ALR2;
use Leysco100\Shared\Models\Administration\Models\ALR3;
use Leysco100\Shared\Models\Administration\Models\ALT1;
use Leysco100\Shared\Models\Administration\Models\ALT2;
use Leysco100\Shared\Models\Administration\Models\ALT3;
use Leysco100\Shared\Models\Administration\Models\ALT4;
use Leysco100\Shared\Models\Administration\Models\ALT5;
use Leysco100\Shared\Models\Administration\Models\ALT6;
use Leysco100\Shared\Models\Administration\Models\OALR;
use Leysco100\Shared\Models\Administration\Models\OALT;
use Leysco100\Shared\Models\Administration\Models\Role;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Administration\Services\AlertsManagerService;

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
        Log::info($request);
        try {
            $validatedData = $request->validate([
                'Code' => 'nullable|string',
                'Name' => 'required|string',
                'Type' => 'required|string',
                'Priority' => 'required|integer',
                'Active' => 'required|boolean',
                'FrqncyType' => 'required|string',
                'FrqncyIntr' => 'required|integer',
                'alt1' => 'required|array',
                'alt2' => 'nullable|array',
                'alt4' => 'nullable|array',
                'alt6' => 'nullable|array',
                'alt5' => 'nullable|array',
            ]);
            $data = (new AlertsManagerService())->processPeriod(
                $request['FrqncyType'],
                $request['FrqncyIntr'],
                $request['ExecTime'],
                $request['ExecDaY']
            );
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
            // OALR::create([
            //     'Code' =>  $data->id,
            //     'Type' =>  $request['Type'] ?? null,
            //     'Priority' =>  $request['Priority'] ?? null,
            //     'TCode' =>  $request['TCode'] ?? null,
            //     'Subject' =>  $request['Subject'] ?? null,
            //     'UserText' =>  $request['UserText'] ?? null,
            //     'DataCols' =>  $request['DataCols'] ?? null,
            //     'DataParams' =>  $request['DataParams'] ?? null,
            //     'MsgData' =>  $request['MsgData'] ?? null,
            //     'DraftEntry' => $request['DraftEntry'] ?? null,
            //     'UserSign' => $request['UserSign'] ?? null,
            //     'Attachment' =>  $request['Attachment'] ?? null,
            //     'DataSource' =>  $request['DataSource'] ?? null,
            //     'AtcEntry' =>  $request['AtcEntry'] ?? null,
            //     'AltType' =>  $request['AltType'] ?? null,
            //     'CompanyID' => $request['CompanyID'] ?? null,
            // ]);
            if ($request['alt1']) {
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
            }
            if ($request['alt2']) {
                foreach ($request['alt2'] as $Group) {
                    ALT2::create([
                        'DocEntry' => $data->id,
                        'Code' =>  $request['Code'] ?? null,
                        'SendIntrnl' =>  $Group['SendIntrnl'] ?? false,
                        'SendEMail' =>  $Group['SendEMail'] ?? false,
                        'SendSMS' =>  $Group['SendSMS'] ?? false,
                        'SendFax' =>  $Group['SendFax'] ?? false,
                        'GroupId' =>   $Group['GroupId']
                    ]);
                }
            }
            if ($request['alt4']) {
                foreach ($request['alt4'] as $id) {
                    ALT4::create([
                        'DocEntry' => $data->id,
                        'UserSign' => Auth::user()->id,
                        'QueryId' =>  $id ?? null
                    ]);
                }
            }
            if ($request['alt5']) {
                $alert_template =  ALT5::create(
                    [
                        'DocEntry' => $data->id,
                        'UserSign' => Auth::user()->id,
                        'tempSubject' => $request['alt5']['tempSubject'] ?? null,
                        'tempTitle' => $request['alt5']['tempTitle'] ?? null,
                        'tempCode' => $request['alt5']['tempCode'] ?? null,
                        'tempBody' => $request['alt5']['tempBody'] ?? null,
                    ]
                );
            }
            if ($request['alt6']) {
                foreach ($request['alt6'] as $atchmt_query) {
                    ALT6::create(
                        [
                            'DocEntry' => $alert_template->id ?? null,
                            'UserSign' => Auth::user()->id,
                            'AlertId' =>  $id,
                            'QueryId' =>  $atchmt_query,
                        ]
                    );
                }
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

            $alertData = OALT::with(
                'alt1.users',
                'alt2',
                'alt3',
                'alt5'
            )
                ->with(['alt4' => function ($query) {
                    $query->select('QueryId as id', 'DocEntry', 'QueryId');
                }])
                ->with(['alt6' => function ($query) {
                    $query->select('QueryId as id', 'DocEntry', 'AlertId');
                }])
                ->where('id', $id)
                ->first();


            if ($alertData) {
                $alertData->alt4 = $alertData->alt4->map->only('QueryId');
                $alertData->alt6 = $alertData->alt6->map->only('QueryId');
                Log::info($alertData->alt4);
            }

            Log::info($alertData);
            return (new ApiResponseService())->apiSuccessResponseService($alertData);
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
                'FrqncyType' => 'required|string',
                'FrqncyIntr' => 'required|integer',
                'Active' => 'required|boolean',
                'alt1' => 'required|array',
                'alt2' => 'nullable|array',
                'alt4' => 'nullable|array',
                'alt6' => 'nullable|array',
                'alt5' => 'nullable|array',
            ]);
            $data = (new AlertsManagerService())->processPeriod(
                $request['FrqncyType'],
                $request['FrqncyIntr'],
                $request['ExecTime'],
                $request['ExecDaY']
            );
            $alert =   OALT::where('id', $id)->update([
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
                'FrqncyType' =>  $request['FrqncyType'],
                'FrqncyIntr' =>  $request['FrqncyIntr'],
                'ExecDaY' =>  $data['ExecDay'] ?? null,
                'ExecTime' =>  $data['ExecTime'] ?? null,
                'LastDate' =>  $request['LastDate'] ?? null,
                'LastTIME' =>  $request['LastTIME'] ?? null,
                'NextDate' =>  $data['NextDate'] ?? null,
                'NextTime' =>  $data['NextTime'] ?? null,
                'UserSign' => Auth::user()->id,
                'History' =>  $request['History'] ?? null,
                'QCategory' =>  $request['QCategory'] ?? null,
            ]);

            if (array_key_exists('alt1',  $validatedData)) {
                $existing = ALT1::where('DocEntry', $id)->pluck('UserSign');
                $updated = collect($request['alt1'])->pluck('UserSign');
                $removed = $existing->diff($updated);
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


            if (array_key_exists('alt2',  $validatedData)) {
                $existing = ALT2::where('DocEntry', $id)->pluck('GroupId');
                $updated = collect($request['alt2'])->pluck('GroupId');
                $removed = $existing->diff($updated);
                foreach ($request['alt2'] as $group) {
                    $group_exists =     ALT2::where('GroupId', $group['GroupId'])->first();
                    if ($group_exists) {
                        ALT2::where('GroupId', $group['GroupId'])->update(
                            [
                                'DocEntry' => $id,
                                'GroupId' => $group['GroupId'],
                                'Code' =>  $request['Code'] ?? null,
                                'SendIntrnl' =>  $group['SendIntrnl'] ?? false,
                                'SendEMail' =>  $group['SendEMail'] ?? false,
                                'SendSMS' =>  $group['SendSMS'] ?? false,
                                'SendFax' =>  $group['SendFax'] ?? false,
                            ]
                        );
                    } else {
                        ALT2::create(
                            [
                                'DocEntry' => $id,
                                'GroupId' => $group['GroupId'],
                                'Code' =>  $request['Code'] ?? null,
                                'SendIntrnl' =>  $group['SendIntrnl'] ?? false,
                                'SendEMail' =>  $group['SendEMail'] ?? false,
                                'SendSMS' =>  $group['SendSMS'] ?? false,
                                'SendFax' =>  $group['SendFax'] ?? false,
                            ]
                        );
                    }
                }
                foreach ($removed as $remvd) {
                    ALT2::where('GroupId', $remvd)->delete();
                }
            }

            //alert queries

            if (is_array($validatedData['alt4']) && array_key_exists('alt4',  $validatedData)) {


                $existing = ALT4::where('DocEntry', $id)->pluck('QueryId');
                $updated = collect($request['alt4']);

                $removed = $existing->diff($updated);

                foreach ($request['alt4'] as $QueryId) {
                    $query_exists =  ALT4::where('QueryId', $QueryId)->first();

                    if ($query_exists) {
                        ALT4::where('QueryId', $QueryId)->update(
                            [
                                'DocEntry' => $id,
                                'QueryId' => $QueryId,
                                'UserSign' => Auth::user()->id,
                            ]
                        );
                    } else {

                        ALT4::create(
                            [
                                'DocEntry' => $id,
                                'QueryId' => $QueryId,
                                'UserSign' => Auth::user()->id,
                            ]
                        );
                    }
                }
                foreach ($removed as $remvd) {
                    ALT4::where('QueryId', $remvd)->delete();
                }
            }
            //alert mail template
            if (array_key_exists('alt5',  $validatedData)) {
                Log::info($request['alt5']);
                $alert_template =   ALT5::where('DocEntry', $id)->update(
                    [
                        'DocEntry' => $id,
                        'UserSign' => Auth::user()->id,
                        'tempSubject' => $request['alt5']['tempSubject'] ?? null,
                        'tempTitle' => $request['alt5']['tempTitle'] ?? null,
                        'tempCode' => $request['alt5']['tempCode'] ?? null,
                        'tempBody' => $request['alt5']['tempBody'] ?? null,
                    ]
                );
            }

            //template attachment query ALT6
            if (is_array($validatedData['alt6']) && array_key_exists('alt6',  $validatedData)) {

                $existing = ALT6::where('AlertId', $id)->pluck('QueryId');
                $updated = collect($request['alt6']);
                $removed = $existing->diff($updated);

                foreach ($request['alt6'] as $atchmt_query) {
                    $query_exists =  ALT6::where('QueryId',  $atchmt_query)->first();
                    if ($query_exists) {
                        ALT6::where('QueryId', $id)->update(
                            [
                                'DocEntry' => $alert_template->id ?? null,
                                'UserSign' => Auth::user()->id,
                                'AlertId' =>  $id,
                                'QueryId' =>  $atchmt_query,
                            ]
                        );
                    } else {
                        ALT6::create(
                            [
                                'DocEntry' => $alert_template->id ?? null,
                                'UserSign' => Auth::user()->id,
                                'AlertId' =>  $id,
                                'QueryId' =>  $atchmt_query,
                            ]
                        );
                    }
                }
                foreach ($removed as $remvd) {
                    ALT6::where('QueryId', $remvd)->delete();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully !!");
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function AlertTemplate($id)
    {
        try {
            $alertTemp = ALT5::where('DocEntry', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($alertTemp);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getAlertVariables()
    {
        try {
            $alert_variable = ALT3::get();
            return (new ApiResponseService())->apiSuccessResponseService($alert_variable);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function createAlertVariables(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'variable_value' => 'nullable|string',
                'variable_key' => 'required|string',
            ]);
            $alert_variable = ALT3::create([
                'variable_key' => $request['variable_key'],
                'variable_value' => $request['variable_value'],
                'UserSign' => Auth::user()->id,
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($alert_variable);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function showAlertVariable(Request $request, $id)
    {
        try {
            $alert_variable = ALT3::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($alert_variable);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function   editAlertVariable(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'variable_value' => 'nullable|string',
                'variable_key' => 'required|string',
            ]);
            $alert_variable = ALT3::where('id', $id)->update([
                'variable_key' => $request['variable_key'],
                'variable_value' => $request['variable_value'],
                'UserSign' => Auth::user()->id,
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($alert_variable);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }



    public function getAlert($id)
    {

        // $emails = ["robertkimaru1998@gmail.com"];
        //   Mail::to($emails)->send(new AlertMail($id));
        //     $alert =   OALR::with('alert_template.alt1.users', 'alert_template.alt2.group')->where('id', $id)->first();
        //     $mails = [];
        //     foreach ($alert->alert_template->alt2 as $alt2) {
        //         $role = Role::find($alt2->group->id);
        //         $usersWithRole = $role->users;
        //         foreach ($usersWithRole as $userWithRole) {
        //             $mails[] = $userWithRole->email;
        //         }
        //     }

        //     foreach ($alert->alert_template->alt1 as $alt) {
        //         $mails[] = $alt->users->email;
        //     }

        //     $uniqueMails = array_values(array_unique($mails));
        //  // return $uniqueMails;
        //     Mail::to($uniqueMails)->send(new AlertMail($id));
    }
}
