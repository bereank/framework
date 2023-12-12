<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ETST;
use Leysco100\Administration\Http\Controllers\Controller;


class TimeSheetsMasterController extends Controller
{
    /**
     * Display a listing of the ETSTs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ETST::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Store a newly created ETST in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        try {
            $validatedData = $request->validate([
                'ObjType' => 'nullable',
                'Code' => 'nullable|string',
                'Name' => 'required|string',
                'CheckInTime' => 'required|date_format:H:i',
                'CheckOutTime' => 'required|date_format:H:i',
                'LogType' => 'nullable|integer',
                'Active' => 'nullable|boolean',
            ]);

            $ETST = ETST::create(
                [
                    'ObjType' => 222 ?? null,
                    'Active' => $request['Active'] ?? null,
                    'Code' => $request['Code'] ?? null,
                    'Name' => $request['Name'] ?? null,
                    'CheckInTime' => $request['CheckInTime'] ?? null,
                    'CheckOutTime' => $request['CheckOutTime'] ?? null,
                    'LogType' => $request['LogType'] ?? null,

                    'UserSign' => Auth::user()->id,
                    'OwnerCode' => Auth::user()->EmpId
                ]
            );

            return (new ApiResponseService())->apiSuccessResponseService("Created Successfullty");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified ETST.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $etst = ETST::findOrFail($id);

            return (new ApiResponseService())->apiSuccessResponseService($etst);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Update the specified ETST in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $etst = ETST::findOrFail($id);

            $validatedData = $request->validate([
                'ObjType' => 'nullable',
                'Active' => 'nullable|boolean',
                'Code' => 'nullable|string',
                'Name' => 'required|string',
                'CheckInTime' => 'required|date_format:H:i',
                'CheckOutTime' => 'required|date_format:H:i',
                'LogType' => 'nullable|integer',

            ]);


            $etst->update([
                'ObjType' => 222 ?? null,
                'Active' => $request['Active'] ?? null,
                'Code' => $request['Code'] ?? null,
                'Name' => $request['Name'] ?? null,
                'CheckInTime' => $request['CheckInTime'] ?? null,
                'CheckOutTime' => $request['CheckOutTime'] ?? null,
                'LogType' => $request['LogType'] ?? null,

                'UserSign' => Auth::user()->id,
                'OwnerCode' => Auth::user()->EmpId
            ]);

            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified ETST from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $etst = ETST::findOrFail($id);

            $etst->delete();

            return (new ApiResponseService())->apiSuccessResponseService("Deleted Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
