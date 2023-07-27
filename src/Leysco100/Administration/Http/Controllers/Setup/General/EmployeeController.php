<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;


use Illuminate\Http\Request;

use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Administration\Http\Controllers\Controller;

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
            $data = OHEM::select('id', 'firstName', 'lastName', 'empID')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
            $employee = OHEM::create([
                'empID' => $request['empID'],
                'lastName' => $request['lastName'],
                'firstName' => $request['firstName'],
                'sex' => $request['sex'],
                'dept' => $request['dept'],
                'UserSign' => $request['UserSign'],
                'UserSign2' => $request['UserSign2'],

            ]);

            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }


   
}
