<?php

namespace App\Http\Controllers\API\Administration\Setup\General;

use App\Domains\Administration\Models\OUDG;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDefaultsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OUDG::with('employee', 'warehouse', 'driver')->get();
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
            $data = OUDG::create([
                'Code' => $request['Code'], // Default Code
                'Name' => $request['Name'], // Default Name
                'Warehouse' => $request['Warehouse'], // Default Warehouse
                'SalePerson' => $request['SalePerson'], // Defaufl Sales Person
                'Driver' => $request['Driver'], //Default Driver
                'DftItmsGrpCod' => $request['DftItmsGrpCod'], // Defautl Item Group
                'BPLId' => $request['BPLId'], // Default Branch
                'CogsOcrCod' => $request['CogsOcrCod'],
                'CogsOcrCo2' => $request['CogsOcrCo2'],
                'CogsOcrCo3' => $request['CogsOcrCo3'],
                'CogsOcrCo4' => $request['CogsOcrCo4'],
                'CogsOcrCo5' => $request['CogsOcrCo5'],
            ]);
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            DB::rollback();
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
            $data = OUDG::with('employee', 'warehouse', 'driver')
                ->where('id', $id)
                ->first();
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
            $details = [
                'Code' => $request['Code'], // Default Code
                'Name' => $request['Name'], // Default Name
                'Warehouse' => $request['Warehouse'],
                'SalePerson' => $request['SalePerson'],
                'Driver' => $request['Driver'],
                'BPLId' => $request['BPLId'], // Default Branch
                'CogsOcrCod' => $request['CogsOcrCod'],
                'CogsOcrCo2' => $request['CogsOcrCo2'],
                'CogsOcrCo3' => $request['CogsOcrCo3'],
                'CogsOcrCo4' => $request['CogsOcrCo4'],
                'CogsOcrCo5' => $request['CogsOcrCo5'],
                'DftItmsGrpCod' => $request['DftItmsGrpCod'], // Defautl Item Group
            ];
            OUDG::where('id', $id)->update($details);
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
