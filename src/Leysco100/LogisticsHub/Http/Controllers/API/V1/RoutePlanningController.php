<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\LogisticsHub\Models\ORPS;
use Leysco100\Shared\Models\LogisticsHub\Models\CRD16;
use Leysco100\LogisticsHub\Http\Controllers\Controller;

class RoutePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = request()->filled('name') ? request()->input('name')  : false;

        $UserSign = request()->filled('UserSign') ? request()->input('UserSign')  : false;

        $OwnerCode = request()->filled('OwnerCode') ? request()->input('OwnerCode')  : false;
        try {
            $record =  ORPS::with('calls.outlet', 'outlets', 'territory')
                ->when($name, function ($query) use ($name) {
                    return $query->where('name',  $name);
                })
                ->when($UserSign, function ($query) use ($UserSign) {
                    return $query->where('UserSign', $UserSign);
                })
                ->when($OwnerCode, function ($query) use ($OwnerCode) {
                    return $query->where('OwnerCode', $OwnerCode);
                })
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($record);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'TerritoryID' => 'required',
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiFailedResponseService($validator->errors()->first());
        }

        try {
            $record =   ORPS::updateOrcreate(
                [
                    "Code" => $request['Code'] ?? null,
                    "StartLng" => $request['StartLng'] ?? null,
                    "StartLat" => $request['StartLat'] ?? null,
                    "EndLat" => $request['EndLat'] ?? null,
                    "EndLng" => $request['EndLng'] ?? null,
                    "StartLocName" => $request['StartLocName'] ?? null,
                    "EndLocName" => $request['EndLocName'] ?? null,
                    "DocNum" => $request['DocNum'] ?? null,
                    "OwnerCode" => $request['OwnerCode'] ?? $user->EmpID,
                    "ObjType" => null,
                    "ExtCode" => $request['ExtCode'] ?? null,
                    "Active" => $request['Active'] ?? true
                ],
                [
                    'name' => $request['name'],
                    'UserSign' => $user->id,
                    'TerritoryID' => $request["TerritoryID"],
                    'Description' => $request['Description'] ? $request['description'] : null,
                    "ObjType" => null,
                    "ExtCode" => $request['ExtCode'] ?? null,
                    "Active" => $request['Active'] ?? null
                ]

            );
            return (new ApiResponseService())->apiSuccessResponseService($record);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function createRouteOutlets(Request $request)
    {
        //Add routeid to ocrds table



        //Creating Rows:
        foreach ($request['bpartners'] as $item) {
            Log::info($item);
            $HeaderItems = CRD16::updateOrcreate(
                [
                    'RouteID' => $request['route_id'],
                    'CardCode' => $item, //Outlet Val
                ]
            );
        }
    }

    public function createRouteCalls(Request $request)
    {
        $user = Auth::user();
        //Creating Calls:
        foreach ($request['outlets'] as $key => $value) {
            $OCLG = OCLG::firstOrCreate([
                'RouteCode' => $request['RouteCode'], // Sales Employee
                'SlpCode' => $request['SlpCode'], // Sales Employee
                'CardCode' => $value['CardCode'], // Oulet/Customer
                'CallDate' => $request['CallDate'], //  Call Date
            ], [
                'CallTime' => $value['StartTime'], // CallTime
                'CallEndTime' => $value['EndTime'], // CallTime
                'Repeat' => $request['Repeat'], // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
                'UserSign' => $user->id,
            ]);
        }

        return (new ApiResponseService())->apiSuccessResponseService(["Calls Created"]);
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
            $record =   ORPS::with('calls.outlet', 'calls.employees', 'outlets','territory')->find($id);
            return (new ApiResponseService())->apiSuccessResponseService($record);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $user = Auth::user();

        $this->validate($request, [
            'name' => 'required', //description
            'TerritoryID' => 'required',
        ]);

        try {
            $record =   ORPS::where('id', $id)->update([
                'name' => $request['name'],
                'UserSign' => $user->id,
                'TerritoryID' => $request["TerritoryID"],
                'Description' => $request['Description'] ? $request['description'] : null,
                "Code" => $request['Code'] ?? null,
                "StartLng" => $request['StartLng'] ?? null,
                "StartLat" => $request['StartLat'] ?? null,
                "EndLat" => $request['EndLat'] ?? null,
                "EndLng" => $request['EndLng'] ?? null,
                "StartLocName" => $request['EndLng'] ?? null,
                "EndLocName" => $request['EndLng'] ?? null,
                "DocNum" => $request['DocNum'] ?? null,
                "OwnerCode" => $request['OwnerCode'] ?? $user->EmpID,
                "ObjType" => null,
                "ExtCode" => $request['ExtCode'] ?? null,
                "Active" => $request['Active'] ?? null
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($record);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            $record =   ORPS::where('id', $id)->update([
                "Active" => false
            ]);
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
