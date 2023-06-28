<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Marketing\Models\GPMGate;


class GateController extends Controller
{

    /**
     *
     */

    public function index()
    {
        $ObjType = 301;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        
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
        $ObjType = 301;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
      
        try {
            $data = GPMGate::create([
                'location_id' => 1,
                'Name' => $request['Name'],
                'Address' => $request['Address'],
                'Longitude' => $request['Longitude'],
                'Latitude' => $request['Latitude'],
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
        $ObjType = 301;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        try {
            $data = GPMGate::where('id', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function update(Request $request, $id)
    {
        $ObjType = 301;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
      
        try {
            $gate = GPMGate::findOrFail($id);
            $gate->update([
                'Name' => $request['Name'],
                'Address' => $request['Address'],
                'Longitude' => $request['Longitude'],
                'Latitude' => $request['Latitude'],
            ]);

            return (new ApiResponseService())->apiSuccessResponseService($gate);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
