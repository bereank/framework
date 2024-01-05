<?php

namespace Leysco100\Finance\Http\Controllers\API\CostAccounting;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Shared\Models\ODIM;
use Leysco100\Shared\Models\Shared\Models\FTR100;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Finance\Http\Controllers\Controller;



class DimensionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ODIM::get();
            foreach ($data as &$val){
                $val->DimStatus = "Inactive";
                if ($val->DimActive == "Y"){
                    $val->DimStatus = "Active";
                }
            }
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
        //
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
            $data = ODIM::where('id', $id)->first();
            $data->DimStatus = false;
            if ($data->DimActive == "Y") {
                $data->DimStatus = true;
            }
            if ($data->DimActive == "N") {
                $data->DimStatus = false;
            }
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
        DB::connection("tenant")->beginTransaction();
        try {
            $data = ODIM::where('id', $id)->first();

            $DimActive = "N";

            if ($request['DimStatus'] == true) {
                $DimActive = "Y";
            }
            $data->update([
                'DimDesc' => $request['DimDesc'],
                'DimActive' => $DimActive,
            ]);

            $tableRowValue = "CogsOcrCod";
            $tableRowValueC = "OcrCode";
            if ($id != 1) {
                $tableRowValue = "CogsOcrCo" . $id;
                $tableRowValueC = "OcrCode" . $id;
            }
            $tabledocument_lines = FTR100::where('value', $tableRowValue)->get();
            foreach ($tabledocument_lines as $key => $val) {
                $val->update([
                    'text' => "COGS " . $request['DimDesc'],
                ]);
            }

            $tabledocument_lines = FTR100::where('value', $tableRowValueC)->get();
            foreach ($tabledocument_lines as $key => $val) {
                $val->update([
                    'text' => $request['DimDesc'],
                ]);
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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
