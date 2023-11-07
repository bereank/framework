<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        $ObjType = 302;
        $user = Auth::user();
        //         $TargetTables = APDI::with('pdi1')
        //         ->where('ObjectID', $ObjType)
        //         ->first();
        //    (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            // $ownerData = [];
            // $dataOwnership = (new AuthorizationService())->CheckIfActive($ObjType, $user->EmpID);
            // if ($dataOwnership) {
            //     $ownerData =  (new AuthorizationService())->getDataOwnershipAuth($ObjType, 1);
            // }

            $data = GPMGate::with('ohem')
                ->with('location')
                // ->when($dataOwnership, function ($query) use ($ownerData) {
                //     return $query->whereIn('OwnerCode', $ownerData);
                // })
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
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
                'location_id' => $request['location_id'] ?? null,
                'Name' => $request['Name'],
                'Address' => $request['Address'],
                'Longitude' => $request['Longitude'],
                'Latitude' => $request['Latitude'],
                'OwnerCode' => $request['OwnerCode']
            ]);



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
            $gate = GPMGate::with('location')->where('id', $id)->first();


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
                'OwnerCode' => $request['OwnerCode'],
                'location_id' => $request['location_id'] ?? null
            ]);

            return (new ApiResponseService())->apiSuccessResponseService($gate);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
