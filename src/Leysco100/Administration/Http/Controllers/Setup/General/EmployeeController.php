<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;

use Illuminate\Http\Request;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Services\ApiResponseService;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $data = OHEM::with('managr:id,jobTitle,firstName,lastName,empID,dept,branch,manager', 'department:id,Code,Name,Remarks')
                ->select('id', 'jobTitle', 'firstName', 'lastName', 'empID', 'dept', 'branch', 'manager')->get();
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
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'middleName' => 'nullable|string',
                'empID' => 'nullable|string',
                'manager' => 'nullable|integer',
                'email' => 'nullable|email',
                'govID' => 'nullable|string',
                'mobile' => 'nullable',
                'ExtEmpNo' => 'nullable|string',
                'jobTitle' => 'nullable|string',
                'Active' => 'nullable|boolean',
                'salesPrson' => 'nullable|integer',
                'sex' => 'nullable',
                'branch' => 'nullable',
                'dept' => 'nullable|integer',
                'userId' => 'nullable'
            ]);

            $employee = OHEM::create([
                'empID' => $request['empID'] ?? "",
                'lastName' => $request['lastName'] ?? "",
                'firstName' => $request['firstName'] ?? "",
                'sex' => $request['sex'] ?? "",
                'dept' => $request['dept'] ?? "",
                'UserSign' => $request['UserSign'] ?? "",
                'userId' => $request['userId'] ?? "",
                'middleName' => $request['middleName'] ?? "",
                'manager' => $request['manager'] ?? "",
                'email' => $request['email'] ?? "",
                'govID' =>  $request['govID'] ?? "",
                'mobile' => $request['mobile'] ?? "",
                'ExtEmpNo' =>  $request['ExtEmpNo'] ?? "",
                'jobTitle' =>  $request['jobTitle'] ?? "",
                'Active' =>  $request['Active'] ?? "",
                'salesPrson' => $request['salesPrson'] ?? "",
                'branch' => $request['branch'] ?? "",
            ]);

            return (new ApiResponseService())->apiSuccessResponseService();
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
            $data =  OHEM::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
                'firstName' => 'required',
                'lastName' => 'required',
                'middleName' => 'nullable',
                'empID' => 'nullable',
                'manager' => 'nullable',
                'email' => 'nullable|email',
                'govID' => 'nullable',
                'mobile' => 'nullable',
                'ExtEmpNo' => 'nullable',
                'jobTitle' => 'nullable',
                'Active' => 'nullable|boolean',
                'salesPrson' => 'nullable',
                'sex' => 'nullable',
                'branch' => 'nullable',
                'dept' => 'nullable',
                'userId' => 'nullable'
            ]);
            $employee = OHEM::findorfail($id);
            $employee->fill([
                'empID' => $request['empID'],
                'lastName' => $request['lastName'],
                'firstName' => $request['firstName'],
                'sex' => $request['sex'],
                'dept' => $request['dept'],
                'UserSign' => $request['UserSign'],
                'userId' => $request['userId'],
                'middleName' => $request['middleName'],
                'manager' => $request['manager'],
                'email' => $request['email'],
                'govID' =>  $request['govID'],
                'mobile' => $request['mobile'],
                'ExtEmpNo' =>  $request['ExtEmpNo'],
                'jobTitle' =>  $request['jobTitle'],
                'Active' =>  $request['Active'],
                'salesPrson' => $request['salesPrson'],
                'branch' => $request['branch'],
            ]);
            $employee->save();
            return (new ApiResponseService())->apiSuccessResponseService();
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
    }
}
