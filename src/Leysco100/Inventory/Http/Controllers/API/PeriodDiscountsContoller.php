<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;


use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP1;

class PeriodDiscountsContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ItemCode =  request()->filled('ItemCode') ? request()->input('ItemCode') : false;
        $ListNum =  request()->filled('ListNum') ? request()->input('ListNum') : false;
        try {
            $data = SPP1::when($ItemCode, function ($query) use ($ItemCode) {
                return $query->where('ItemCode', $ItemCode);
            })
                ->when($ListNum, function ($query) use ($ListNum) {
                    return $query->where('ListNum', $ListNum);
                })
                ->get();
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

            $validatedData = $request->validate([
                'ItemCode' => 'nullable',
                'CardCode' => 'nullable',
                'LINENUM' => 'nullable|integer',
                'Price' => 'nullable|numeric',
                'Currency' => 'nullable',
                'Discount' => 'nullable|numeric',
                'ListNum' => 'nullable|integer',
                'FromDate' => 'nullable|date',
                'ToDate' => 'nullable|date',
                'AutoUpdt' => 'nullable|boolean',
                'Expand' => 'nullable|boolean',
            ]);

            $data = SPP1::create($validatedData);
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = OPLN::with('itm1.item')
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
        $details = [
            'ListName' => $request['ListName'],
            'ValidFor' => $request['ValidFor'],
            'PrimCurr' => $request['PrimCurr'],
            'BASE_NUM' => $request['BASE_NUM'], // Base Price List
            'Factor' => $request['Factor'], // Default Factor
            'AddCurr1' => $request['AddCurr1'],
            'AddCurr2' => $request['AddCurr2'],
        ];
        OPLN::where('id', $id)->update($details);

        return response()
            ->json(
                [
                    'message' => "Updated Successfully",
                ],
                201
            );
    }
    public function getUomPrices($id)
    {

        try {
            $data = ITM9::where('ItemCode', $id)
                ->with('uom')
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
