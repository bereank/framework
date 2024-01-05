<?php

namespace Leysco100\Finance\Http\Controllers\API\CostAccounting;

use Illuminate\Http\Request;
use Leysco100\Finance\Http\Controllers\Controller;
use Leysco100\Shared\Models\SalesOportunities\Models\OOCR;
use Leysco100\Shared\Services\ApiResponseService;

class DistributionRulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $DimCode = \Request::get('DimCode');

        try {
            $data = OOCR::with('odim')
                ->where(function ($q) use ($DimCode) {
                    if ($DimCode) {
                        $q->where('DimCode', $DimCode)->where('Active', "Y");
                    }
                })

                ->get();
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
            $data = OOCR::with('odim', 'oprc','ocr1.oprc')->where('id', $id)->first();
            $data->ValidFrom = $data->oprc->ValidFrom;
            $data->ValidTo = $data->oprc->ValidTo;
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
        //
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
