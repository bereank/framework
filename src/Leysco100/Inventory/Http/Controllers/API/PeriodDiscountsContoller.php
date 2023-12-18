<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSPP;
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
        DB::connection("tenant")->beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'ItemCode' => 'required',
                'Discount' => 'required',
                'FromDate' => 'required',
                'ToDate' => 'required',
                'ListNum' => 'required',
            ]);
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
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
        Log::info($request);
        DB::connection("tenant")->beginTransaction();
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

            $data = SPP1::where('id', $id)->update([
                'ItemCode' => $request['ItemCode'],
                'CardCode' => $request['CardCode'] ?? null,

                'Price' => $request['Price'] ?? 0,
                'Currency' => $request['Currency'] ?? 'Kes',
                'Discount' => $request['Discount'] ?? 0,

                'FromDate' => $request['FromDate'] ?? null,
                'ToDate' => $request['ToDate'] ?? null,
                'AutoUpdt' => $request['AutoUpdt'] ?? 1,

            ]);
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
    public function periodDiscountItems(Request $request, $id)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $searchTerm = $request->input('search') ? $request->input('search') : false;
            $data = OSPP::with('oitm')->where('ListNum', $id);
            if ($searchTerm) {
                Log::info("data");

                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->orWhereDate('created_at', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('ItemCode', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('CardCode', 'LIKE', "%{$searchTerm}%");
                });
            }
            $data = $data->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $data = SPP1::where('id', $id)
                ->delete();

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
