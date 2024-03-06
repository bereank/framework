<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Administration\Http\Controllers\Controller;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::connection("tenant")->beginTransaction();
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
                'AddToFavourites' => $request['AddToFavourites'] ?? 0,
                'DftBinLoc' => $request['DftBinLoc'] ?? null,
                'EtstCode' => $request['EtstCode'] ?? null,
                'RouteID' => $request['RouteID'] ?? null, //Default Route
                'RouteActive' => $request['RouteActive'] == true ? 1 : 0,
                'SellFromBin' => $request['SellFromBin'] == true ? 1 : 0,
                'GpsSetUpID' => $request['GpsSetUpID'] ?? null, //Gps Setup
                'GpsActive' => $request['GpsActive'] == true ? 1 : 0,
                'EdtItmPrc' => $request['EdtItmPrc'] == true ? 1 : 0,
                'MultiLogin' => $request['MultiLogin'] == true ? 1 : 0
            ]);
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::connection("tenant")->beginTransaction();
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
                'AddToFavourites' => $request['AddToFavourites'] ?? 0,
                'DftBinLoc' => $request['DftBinLoc'] ?? null,
                'EtstCode' => $request['EtstCode'] ?? null,
                'ClockIn' => $request['ClockIn'] ?? null,
                'RouteID' => $request['RouteID'] ?? null,
                'RouteActive' => $request['RouteActive'] == true ? 1 : 0,
                'SellFromBin' => $request['SellFromBin'] == true ? 1 : 0,
                'GpsSetUpID' => $request['GpsSetUpID'] ?? null, //Gps Setup
                'GpsActive' => $request['GpsActive'] == true ? 1 : 0,
                'EdtItmPrc' => $request['EdtItmPrc'] == true ? 1 : 0,
                'MultiLogin' => $request['MultiLogin'] == true ? 1 : 0
            ];
            $data = OUDG::where('id', $id)->update($details);

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
