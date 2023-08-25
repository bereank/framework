<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Leysco100\MarketingDocuments\Jobs\InventoryUpdateJob;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;



class IInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = OWHS::get();
        return $data;
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
        $newWaharehouse = OWHS::firstOrCreate([
            'WhsCode' => $request['WhsCode'],
        ], [
            'WhsName' => $request['WhsName'],
        ]);

        return $newWaharehouse;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            InventoryUpdateJob::dispatch($request['data']);
        } catch (\Throwable $th) {
            Log::info($th);
            throw $th;
        }
    }

    public function updateCostCentreQuantities(Request $request)
    {

        // 'ItemCode' => 'SZ100TVS STAR LX-L',
        // 'InQty' => 0.0,
        // 'OutQty' => 2.0,
        // 'Warehouse' => 'KIS01',
        // 'PrjCode' => NULL,
        // 'OcrCode' => 'LKM003',
        // 'OcrCode2' => 'D001',
        // 'OcrCode3' => 'P002',
        // 'OcrCode4' => NULL,
        // 'OcrCode5' => NULL,
        // 'created_at' => '0001-01-01T00:00:00',
        // 'updated_at' => '0001-01-01T00:00:00',
        // 'SAPDate' => '2021-11-15T00:51:21.3538648+03:00',
    }
}
