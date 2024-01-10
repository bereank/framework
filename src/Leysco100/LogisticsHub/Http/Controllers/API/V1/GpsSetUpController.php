<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\OGPS;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\LogisticsHub\Models\Expense;




class GpsSetUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OGPS::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
        try {

            $this->validate($request, [
                'max_latitude' => 'required',
                'min_latitude' => 'required',
                'max_longitude' => 'required',
                'min_longitude' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'UpdtFrq' => 'required',
            ]);

            $gpsSetting = OGPS::create(
                [
                    'Name' => $request['Name'] ?? null,
                    'DocNum' => $request['DocNum'] ?? null,
                    'UserSign' => Auth::user()->id,
                    'OwnerCode' => Auth::user()->EmpId ?? null,
                    'ObjType' => null,
                    'UpdtFrq' => $request['UpdtFrq'] ?? null,
                    'max_latitude' => $request['max_latitude'] ?? null,
                    'min_latitude' => $request['min_latitude'] ?? null,
                    'max_longitude' => $request['max_longitude'] ?? null,
                    'min_longitude' => $request['min_longitude'] ?? null,
                    'Active' => $request['Active'] ?? null,
                    'start_time' => $request['start_time'] ?? null,
                    'end_time' => $request['end_time'] ?? null,
                    'ExtCode' => $request['ExtCode'] ?? null,
                ]
            );

            return (new ApiResponseService())->apiSuccessResponseService($gpsSetting);
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

            $gpsSetting = OGPS::where('id', $id)->first();

            return (new ApiResponseService())->apiSuccessResponseService($gpsSetting);
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
        try {
            $this->validate($request, [
                'max_latitude' => 'required',
                'min_latitude' => 'required',
                'max_longitude' => 'required',
                'min_longitude' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'UpdtFrq' => 'required',
            ]);

            $gpsSetting = OGPS::where('id', $id)->update(
                [
                    'Name' => $request['Name'] ?? null,
                    'DocNum' => $request['DocNum'] ?? null,
                    'UserSign' => Auth::user()->id,
                    'OwnerCode' => Auth::user()->EmpId ?? null,
                    'ObjType' => null,
                    'UpdtFrq' => $request['UpdtFrq'] ?? null,
                    'max_latitude' => $request['max_latitude'] ?? null,
                    'min_latitude' => $request['min_latitude'] ?? null,
                    'max_longitude' => $request['max_longitude'] ?? null,
                    'min_longitude' => $request['min_longitude'] ?? null,
                    'Active' => $request['Active'] ?? null,
                    'start_time' => $request['start_time'] ?? null,
                    'end_time' => $request['end_time'] ?? null,
                    'ExtCode' => $request['ExtCode'] ?? null,
                ]
            );

            return (new ApiResponseService())->apiSuccessResponseService($gpsSetting);
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
        try {
            $gpsSetting = OGPS::where('id', $id)->delete();
            return (new ApiResponseService())->apiSuccessResponseService($gpsSetting);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
