<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\LogisticsHub\Models\ORAS;
use Leysco100\Shared\Services\ApiResponseService;

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
