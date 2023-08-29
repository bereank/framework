<?php

namespace Leysco100\Finance\Http\Controllers\API;

use App\Domains\SalesOportunities\Models\OCR1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Finance\Models\OPRC;
use Leysco100\Shared\Models\SalesOportunities\Models\OOCR;
use Leysco100\Shared\Models\Shared\Models\ODIM;
use Leysco100\Shared\Services\ApiResponseService;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OPRC::with('odim', 'costcentertype')->get();
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
        DB::beginTransaction();
        try {
            $data = OPRC::create([
                'PrcCode' => $request['PrcCode'],
                'PrcName' => $request['PrcName'],
                'DimCode' => $request['DimCode'],
            ]);

            $distributionRules = OOCR::where('DimCode', $request['DimCode'])->first();

            if (!$distributionRules) {
                $newDist = OOCR::create([
                    'OcrCode' => $request['PrcCode'],
                    'OcrName' => $request['PrcName'],
                    'OcrTotal' => 100,
                    'DimCode' => $request['DimCode'],
                ]);

                $newDist1 = OCR1::create([
                    'OcrCode' => $newDist->id,
                    'PrcCode' => $data->id,
                    'PrcAmount' => 100,
                    'OcrTotal' => 100,
                ]);
            }
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = OPRC::where('id', $id)->with('odim')->first();
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
            $data = ODIM::where('id', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
