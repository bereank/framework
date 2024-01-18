<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\LogisticsHub\Services\RouteMgtService;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\LogisticsHub\Models\ORAS;
use Leysco100\Shared\Models\LogisticsHub\Models\CRD16;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;

class RouteAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //        $this->validate($request, [
        //            'date' => 'required',
        //        ]);

        $date = $request["date"];

        $SlpCode = $request["SlpCode"];
        try {
            $assignments = ORAS::with('route.outlets', 'oslp')
                ->where(function ($q) use ($SlpCode, $date) {
                    if ($SlpCode) {
                        $q->where("SLPCode", $SlpCode);
                    }
                    if ($date) {
                        $q->where("Date", $date);
                    }
                })
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($assignments);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'SlpCode' => 'required',
            'RouteID' => 'required',
        ]);
        $user = Auth::user();

        DB::connection("tenant")->beginTransaction();
        try {

            $assignment =  ORAS::updateOrcreate(
                [
                    'SlpCode' => $request['SlpCode'],
                    'RouteID' => $request['RouteID'],
                    'Date' => $request['date'],
                    'Code' => $request['Code'] ?? null,
                ],
                [
                    'Name' => $request['Name'] ?? null,
                    'Description' => $request['Description'] ?? null,
                    'Repeat' => $request['Repeat'] ?? null,
                    'UserSign' => $user->id,
                    "OwnerCode" => $request['OwnerCode'] ?? $user->EmpID,
                    "ObjType" => null,
                    "DocNum" => $request['DocNum'] ?? false,
                    "CreateCall" => $request['CreateCall'] ?? false,
                    "Active" => $request['Active'] ?? null
                ]
            );
            $Codes = CRD16::where('RouteID', $request['RouteID'])->pluck('CardCode');
            $CardCodes =   OCRD::whereIn('id', $Codes)->pluck('CardCode');
            foreach ($CardCodes as $CardCode) {
                $otherCalls = OCLG::whereDate('CallDate', $request['date'])
                    ->where('CardCode', $CardCode)
                    ->first();

                if ($request['CreateCall'] && empty($otherCalls)) {
                    $call = [
                        'ClgCode' => null,
                        'SlpCode' => $request['SlpCode'],
                        'CardCode' => $CardCode, //Customer
                        'CallDate' => $request['date'], //  Call Date
                        'CallTime' => null, // CallTime
                        'UserSign' => $user->id,
                        'RouteCode' => $request['RouteID'] ?? null,
                        'CallEndTime' =>  null,
                        'CloseDate' =>  null,
                        'CloseTime' =>   null,
                        'Repeat' =>  "N",
                        'Summary' =>  null,
                        'Status' => 'D',
                    ];
                    $OCLG = (new RouteMgtService())->CreateCallsService($call);
                }
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($assignment);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $assignments = ORAS::with('route.outlets', 'oslp')
                ->find($id);
            return (new ApiResponseService())->apiSuccessResponseService($assignments);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'SlpCode' => 'required',
            'RouteID' => 'required',
        ]);
        $user = Auth::user();

        DB::connection("tenant")->beginTransaction();
        try {

            $assignment =  ORAS::where('id', $id)->update(
                [
                    'SlpCode' => $request['SlpCode'],
                    'RouteID' => $request['RouteID'],
                    'Date' => $request['date'],
                    'Repeat' => $request['Repeat'],
                    'UserSign' => $user->id,
                    "OwnerCode" => $request['OwnerCode'] ?? $user->EmpID,
                    "ObjType" => null,
                    "DocNum" => $request['DocNum'] ?? false,
                    "CreateCall" => $request['CreateCall'] ?? false,
                    "Active" => $request['Active'] ?? null,
                    'Code' => $request['Code'] ?? null,
                    'Name' => $request['Name'] ?? null,
                    'Description' => $request['Description'] ?? null,
                ]
            );

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($assignment);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
