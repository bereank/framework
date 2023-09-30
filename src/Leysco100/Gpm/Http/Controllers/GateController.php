<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\MarketingDocuments\Models\GPMGate;
use Leysco100\Shared\Models\MarketingDocuments\Models\NotifyUser;

class GateController extends Controller
{

    /**
     *
     */

    public function index()
    {
//         $ObjType = 302;
//         $TargetTables = APDI::with('pdi1')
//         ->where('ObjectID', $ObjType)
//         ->first();
//    (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = GPMGate::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function store(Request $request)
    {
//         $ObjType = 302;
//         $TargetTables = APDI::with('pdi1')
//         ->where('ObjectID', $ObjType)
//         ->first();
//    (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'create');
        try {
            $data = GPMGate::create([
                'location_id' => 1,
                'Name' => $request['Name'],
                'Address' => $request['Address'],
                'Longitude' => $request['Longitude'],
                'Latitude' => $request['Latitude'],
            ]);

            foreach( $request['userSigns'] as $user ){
                $data = NotifyUser::FirstorCreate([
                    'gate_id' => $data->id,
                    'UserSign' => $user
                ]);
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function show($id)
    {
//         $ObjType = 302;
//         $TargetTables = APDI::with('pdi1')
//         ->where('ObjectID', $ObjType)
//         ->first();
//    (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $gate = GPMGate::with('notify_users')->where('id', $id)->first();

            if ($gate) {
                $userSigns = $gate->notify_users->pluck('UserSign');
        
                $gate->userSigns = $userSigns;
            }
            
          
            return (new ApiResponseService())->apiSuccessResponseService($gate);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function update(Request $request, $id)
    {
//         $ObjType = 302;
//         $TargetTables = APDI::with('pdi1')
//         ->where('ObjectID', $ObjType)
//         ->first();
//    (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'update');
        try {
            $gate = GPMGate::findOrFail($id);
            $gate->update([
                'Name' => $request['Name'],
                'Address' => $request['Address'],
                'Longitude' => $request['Longitude'],
                'Latitude' => $request['Latitude'],
            ]);
            $users= NotifyUser::where('gate_id',$id)->pluck('UserSign')->toArray();
        
            $new= $request['userSigns'] ;
            $diff= array_diff($users,$new);
           
            NotifyUser::whereIn('UserSign',$diff)->delete();
            foreach( $request['userSigns'] as $user ){
              NotifyUser::FirstorCreate([
                    'gate_id' => $gate->id,
                    'UserSign' => $user
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService($gate);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}