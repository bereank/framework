<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP2;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP3;

class VolumeDiscountsContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ItemCode =  request()->filled('ItemCode') ? request()->input('ItemCode') : false;
        $SPP1LNum =  request()->filled('SPP1LNum') ? request()->input('SPP1LNum') : false;
        try {
            $data = SPP2::when($ItemCode, function ($query) use ($ItemCode) {
                return $query->where('ItemCode', $ItemCode);
            })
                ->when($SPP1LNum, function ($query) use ($SPP1LNum) {
                    return $query->where('SPP1LNum', $SPP1LNum);
                })
                ->with('uom')
                ->with('spp3.item')
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
        Log::info($request);
        try {

            $validatedData = $request->validate([
                'ItemCode' => 'required',
                'CardCode' => 'nullable',
                'SPP1LNum' => 'required',
                'Amount' => 'required',
                'Price' => 'nullable',
                'Currency' => 'nullable',
                'Discount' => 'nullable',
                'DiscType' => 'required',
                'UomEntry' => 'nullable',
            ]);

            // Create a new record in the database
            $data = SPP2::create($validatedData);

            foreach ($request['fields'] as $field) {
                if ($field['ItemCode']) {
                    SPP3::create([
                        'ItemCode' => $field['ItemCode'] ?? null,
                        'CardCode' => $field['CardCode'] ?? null,
                        'SPP2Num' =>  $data->id,
                        'MaxForFre' => $field['MaxForFre'] ?? null,
                        'Quantity' => $field['Quantity'] ?? 1,
                        'Price' => 0,
                        'Currency' => $field['Currency'] ?? 'KES',
                        'UomEntry' => $field['UomEntry'] ?? null,
                    ]);
                }
            }
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

        $ItemCode =  request()->filled('ItemCode') ? request()->input('ItemCode') : false;
        $SPP1LNum =  request()->filled('SPP1LNum') ? request()->input('SPP1LNum') : false;
        try {
            $data = SPP2::when($ItemCode, function ($query) use ($ItemCode) {
                return $query->where('ItemCode', $ItemCode);
            })
                ->when($SPP1LNum, function ($query) use ($SPP1LNum) {
                    return $query->where('SPP1LNum', $SPP1LNum);
                })
                ->get();
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
        $validatedData = $request->validate([
            'ItemCode' => 'required',
            'CardCode' => 'nullable',
            'SPP1LNum' => 'nullable',
            'Amount' => 'nullable',
            'Price' => 'nullable',
            'Currency' => 'nullable',
            'Discount' => 'nullable',
            'UomEntry' => 'nullable',
        ]);

        SPP2::where('id', $id)->update($validatedData);

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
    public function destroy($id)
    {
        try {
            $data = SPP2::where('id', $id)
                ->delete();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
