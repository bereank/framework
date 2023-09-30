<?php

namespace Leysco100\Gpm\Http\Controllers\API;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModGates;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModUsers;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;


class BackupModeProcessController
{
    public function index()
    {
        // $ObjType = 215;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = BackUpModeSetup::with('creator')->get();

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        // $ObjType = 215;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'create');
        try {
            $id = Auth::user()->id;
            $startTime = DateTime::createFromFormat('Y-m-d', $request['StartDate']);
            if (!$startTime) {
                return (new ApiResponseService())->apiFailedResponseService('Error, Specify start time');
            } else {
                // Use the converted start time in your logic
                $EndTime = Carbon::instance($startTime);
            }
            $EndTime->addHours($request['Hours']);
            $EndTime->addMinutes($request['Minutes']);

            $data = BackUpModeSetup::create([
                'UserSign' => $id,
                'ObjectType' => $request['ObjectType'] ?? 215,
                'Enabled' => $request['Enabled'],
                'StartDate' => $request['StartDate'],
                'StartTime' => $request['StartTime'],
                'EndTime' =>  $EndTime->format('Y-m-d H:i:s'),
                'Hours' => $request['Hours'],
                'Minutes' => $request['Minutes'],
                'OwnerID' => $id,
                'FieldsTemplate' => $request['template'],
            ]);
            if (!empty($request['Type'])) {
                if (!empty($request['users'])) {
                    foreach ($request['users'] as $id) {
                        $users = BackUpModUsers::create([
                            'UserSign' => $id,
                            'BackupModeID' => $data->id,
                        ]);
                    }
                }

                if (!empty($request['gates'])) {
                    foreach ($request['gates'] as $gate) {
                        $gates = BackUpModGates::create([
                            'GateID' => $gate,
                            'BackupModeID' => $data->id,
                        ]);
                    }
                }
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show($id)
    {

        // $ObjType = 215;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = BackUpModeSetup::with('creator', 'template')
                ->with(['users' => function ($query) {
                    $query->select('UserSign as id', 'BackupModeID');
                }])
                ->with(['gates' => function ($query) {
                    $query->select('GateID as id', 'BackupModeID');
                }])
                ->where('id', $id)
                ->first();

            $users = $data->users->pluck('id')->toArray();
            $gates = $data->gates->pluck('id')->toArray();

            $data->unsetRelation('users');
            $data->unsetRelation('gates');

            $data->users = $users;
            $data->gates = $gates;

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        // $ObjType = 215;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'update');
        try {
            $user_id = Auth::user()->id;
            $startTime = DateTime::createFromFormat('Y-m-d', $request['StartDate']);
            if (!$startTime) {
                return (new ApiResponseService())->apiFailedResponseService('Error, Specify start time');
            } else {
                // Use the converted start time in your logic
                $EndTime = Carbon::instance($startTime);
            }

            $EndTime->addHours($request['Hours']);
            $EndTime->addMinutes($request['Minutes']);


            $data = BackUpModeSetup::find($id);
            if ($data) {
                $data->UserSign =   $user_id;
                $data->ObjectType = $request['ObjectType'] ?? 215;
                $data->Enabled = $request['Enabled'];
                $data->Hours = $request['Hours'];
                $data->Minutes = $request['Minutes'];
                $data->StartTime = $request['StartTime'];
                $data->StartDate = $request['StartDate'];
                $data->EndTime = $EndTime->format('Y-m-d H:i:s');
                $data->OwnerID =   $user_id;
                $data->FieldsTemplate = $request['FieldsTemplate'];
                $data->save();
            }

            if (!empty($request['Type'])) {
                BackUpModUsers::where('BackupModeID', $id)->delete();
                foreach ($request['users'] as $usr) {
                    $users = BackUpModUsers::create([
                        'UserSign' => $usr,
                        'BackupModeID' => $id,
                    ]);
                }
            }

            if (!empty($request['gates'])) {
                BackUpModGates::where('BackupModeID', $id)->delete();
                foreach ($request['gates'] as $gate) {
                    $gates = BackUpModGates::create([
                        'GateID' => $gate,
                        'BackupModeID' => $id,
                    ]);
                }
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
