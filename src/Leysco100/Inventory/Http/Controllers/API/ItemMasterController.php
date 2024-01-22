<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\ITG1;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITG;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITW;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM15;
use Leysco100\MarketingDocuments\Http\Controllers\API\PriceCalculationController;

class ItemMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //        (new AuthorizationService())->checkIfAuthorize(3, 'read');
        try {
            $search = \Request::get('f');

            //            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
            //                ->with('inventoryuom', 'salesuom', 'purchaseuom', 'ougp')
            //                ->where(function ($q) use ($search) {
            //                    if ($search != null) {
            //                        $q->where('ItemCode', 'LIKE', "%{$search}%");
            //                    }
            //                })
            //                ->take(50)
            //                ->orderBy('ItemCode', 'asc')
            //                ->get();
            $data = OITM::select('id', 'ItemName', 'ItemCode')
                //                ->with('inventoryuom', 'salesuom', 'purchaseuom', 'ougp')
                ->where(function ($q) use ($search) {
                    if ($search != null) {
                        $q->where('ItemCode', 'LIKE', "%{$search}%");
                    }
                })
                ->take(50)
                ->orderBy('ItemCode', 'asc')
                ->get();
            foreach ($data as $key => $val) {
                $val->ItemDesc = $val->ItemCode . "     ----  " . $val->ItemName;
                $val->VatGourpSa = $val->VatGourpSa ?? OADM::where('id', 1)->value('DfSVatItem');
                $val->VatGroupPu = $val->VatGroupPu ?? OADM::where('id', 1)->value('DfPVatItem');
                $val->OnHand = OITW::where('ItemCode', $val->ItemCode)->sum('OnHand');
                $val->IsCommited = OITW::where('ItemCode', $val->ItemCode)->sum('IsCommited');
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function inventory_report(Request $request)
    {
        (new AuthorizationService())->checkIfAuthorize(3, 'read');
        try {

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            $skip = 0;
            if ($page >  1) {
                $skip = $page * $perPage;
            }

            $search = \Request::get('search');
            $itemcodesearch = \Request::get('itemcode');
            $whsecodesearch = \Request::get('whsecode');
            $whsecodesearch = \Request::get('whsecode');
            $count = \Request::get('countt');



            //            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
            //                ->with('inventoryuom', 'salesuom', 'purchaseuom', 'ougp')
            //                ->where(function ($q) use ($search) {
            //                    if ($search != null) {
            //                        $q->where('ItemCode', 'LIKE', "%{$search}%");
            //                    }
            //                })
            //                ->take(50)
            //                ->orderBy('ItemCode', 'asc')
            //                ->get();


            //            $data = OITW::select('o_i_t_w_s.id', 'o_i_t_w_s.ItemCode','a.ItemName', 'o_i_t_w_s.WhsCode', 'o_i_t_w_s.IsCommited', 'o_i_t_w_s.OnHand', 'o_i_t_w_s.OnOrder')
            $oitwdata = DB::connection("tenant")->table('o_i_t_w_s')
                ->selectRaw("id, ItemCode,WhsCode, IsCommited, OnHand, OnOrder")
                ->where(function ($q) use ($search, $itemcodesearch, $whsecodesearch) {
                    if ($search != null) {
                        $q->orWhere('ItemCode', 'LIKE', "%{$search}%")
                            ->orWhere("%{$search}%", 'LIKE', 'ItemCode')
                            ->orWhere('WhsCode', 'LIKE', "%{$search}%")
                            ->orWhere("%{$search}%", 'LIKE', 'WhsCode');
                        //                            ->orWhere('a.ItemName', 'LIKE', "%{$search}%")
                        //                            ->orWhere("%{$search}%", 'LIKE','a.ItemName');
                    }
                    if ($itemcodesearch != null) {
                        $q->orWhere('ItemCode', 'LIKE', "%{$itemcodesearch}%")
                            ->orWhere("%{$itemcodesearch}%", 'LIKE', 'ItemCode');
                    }
                    if ($whsecodesearch != null) {
                        $q->orWhere('WhsCode', 'LIKE', "%{ $whsecodesearch}%")
                            ->orWhere("%{ $whsecodesearch}%", 'LIKE', 'WhsCode');
                    }
                })
                ->take($count ?? 1000)
                ->orderBy('ItemCode', 'asc')
                ->toSql();


            $data = DB::connection("tenant")->table('o_i_t_m_s')

                ///->join(DB::raw("({$services} as services)"), 'services.customer_id', '=', 'customers.customer_id')
                ->join(DB::raw("({$oitwdata}) as oitwdata"), 'oitwdata.ItemCode', '=', 'o_i_t_m_s.ItemCode', 'right outer')
                ->select('oitwdata.id', 'oitwdata.ItemCode', 'o_i_t_m_s.ItemName', 'oitwdata.WhsCode', 'oitwdata.IsCommited', 'oitwdata.OnHand', 'oitwdata.OnOrder')
                //                ->get();
                ->paginate($perPage, ['*'], 'page',  $page);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function serials_report(Request $request)
    {
        (new AuthorizationService())->checkIfAuthorize(3, 'read');
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            $skip = 0;
            if ($page >  1) {
                $skip = $page * $perPage;
            }

            $search = \Request::get('search');
            //            $itemcodesearch = \Request::get('itemcode');
            //            $ItemNamesearch = \Request::get('ItemName');
            //            $serrialnumbersearch = \Request::get('serialnumber');
            //            $count = \Request::get('count');

            $osrnData = DB::connection("tenant")->table("o_s_r_n_s")
                ->select('id', 'ItemCode', 'DistNumber', 'SysNumber', 'InDate', 'ItemName')
                //                ->where(function ($q) use ($search, $itemcodesearch, $ItemNamesearch,$serrialnumbersearch ) {
                ->where(function ($q) use ($search) {
                    if ($search != null) {
                        $q->orWhere('ItemCode', 'LIKE', "%{$search}%");
                        //                            ->orWhere("%{$search}%", 'LIKE', 'ItemCode')
                        //                            ->orWhere('ItemName', 'LIKE', "%{$search}%")
                        //                            ->orWhere("%{$search}%", 'LIKE', 'ItemName')
                        //                            ->orWhere('DistNumber', 'LIKE', "%{$search}%")
                        //                            ->orWhere("%{$search}%", 'LIKE', 'DistNumber')
                        //                            ->orWhere('SysNumber', 'LIKE', "%{$search}%");
                        //                            ->orWhere("%{$search}%", 'LIKE', 'SysNumber');

                    }
                    //                    if ($itemcodesearch != null) {
                    //                        $q->orWhere('ItemCode', 'LIKE', "%{$itemcodesearch}%")
                    //                            ->orWhere("%{$itemcodesearch}%", 'LIKE','ItemCode' );
                    //                    }
                    //                    if ( $ItemNamesearch != null) {
                    //                        $q->orWhere('ItemName', 'LIKE', "%{ $ItemNamesearch}%")
                    //                            ->orWhere("%{ $ItemNamesearch}%", 'LIKE','ItemName');
                    //                    }
                    //                    if ( $serrialnumbersearch != null) {
                    //                        $q->orWhere('DistNumber', 'LIKE', "%{ $serrialnumbersearch}%")
                    //                            ->orWhere("%{ $serrialnumbersearch}%", 'LIKE','DistNumber');
                    //                    }
                })
                ->take($count ?? 1000)
                ->orderBy('ItemCode', 'asc')
                ->orderBy('DistNumber', 'asc')
                ->toSql();

            $data = DB::connection("tenant")->table('o_s_r_q_s')
                ->join(DB::raw("({$osrnData}) as osrnData"), function ($join) {
                    $join->on('osrnData.ItemCode', '=', 'o_s_r_q_s.ItemCode')
                        ->on('osrnData.SysNumber', '=', 'o_s_r_q_s.SysNumber');
                })
                ->select(
                    'o_s_r_q_s.id',
                    'o_s_r_q_s.ItemCode',
                    'osrnData.ItemName',
                    'o_s_r_q_s.WhsCode',
                    'o_s_r_q_s.Quantity',
                    'o_s_r_q_s.CommitQty',
                    'osrnData.DistNumber',
                    'osrnData.SysNumber',
                    DB::raw('(CASE
        WHEN o_s_r_q_s.Quantity = 0 THEN "Unavailable"
        WHEN o_s_r_q_s.CommitQty = 1 AND o_s_r_q_s.Quantity = 1 THEN "Allocated"
        WHEN o_s_r_q_s.Quantity = 1  THEN "Available"
        ELSE "Unknown"
        END) AS Status')
                )
                ->orderBy('o_s_r_q_s.ItemCode', 'asc')
                ->orderBy('osrnData.DistNumber', 'asc')
                ->orderBy('o_s_r_q_s.WhsCode', 'asc')
                ->paginate($perPage, ['*'], 'page',  $page);

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
        $validator = Validator::make($request->all(), [
            'ItemName' => 'required',
            'Series' => 'required',
        ]);
        if ($validator->fails()) {
            return (new ApiResponseService())->apiFailedResponseService("Failed");
        }

        try {
            $user = Auth::user();
            $nnm1 = NNM1::where('id', $request['Series'])->first();
            if ($nnm1->IsManual == "Y") {
                $ItemCode = $request['ItemCode'];
                //If Manual=
                if (!$ItemCode) {
                    return (new ApiResponseService())
                        ->apiFailedResponseService("Item No. Missing");
                }
            } else {
                $ItemCode = $nnm1->BeginStr . sprintf("%0" . $nnm1->NumSize . "s", $nnm1->NextNumber) . $nnm1->EndStr;
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }

        DB::connection("tenant")->beginTransaction();
        try {
            $newItem = OITM::create([
                'ItemCode' => $ItemCode,
                'ItemName' => $request['ItemName'],
                'FrgnName' => $request['FrgnName'],
                'ItemType' => $request['ItemType'],
                'ItmsGrpCod' => $request['ItmsGrpCod'],
                'UgpEntry' => $request['UgpEntry'],
                'PriceUnit' => $request['PriceUnit'],
                'InvntItem' => $request['InvntItem'],
                'SellItem' => $request['SellItem'],
                'PrchseItem' => $request['PrchseItem'],
                //General
                'ManBtchNum' => $request['ManBtchNum'], //Manage Batch No. [Yes/No]
                'ManSerNum' => $request['ManSerNum'], //Serial No. Management . [Yes/No]
                //purchse
                'BuyUnitMsr' => $request['BuyUnitMsr'], //Puchasing UoM Name
                'NumInBuy' => $request['NumInBuy'],
                'PurPackUn' => $request['PurPackUn'], //Quantity per Package (Purchasing)
                'VatGroupPu' => $request['VatGroupPu'], //    Purchase Tax Definition
                'PUoMEntry' => $request['PUoMEntry'], //Default Purchase UoM
                'CardCode' => $request['CardCode'], //Preffered Vendor
                //sales
                'SUoMEntry' => $request['SUoMEntry'], //Sales UoM Code
                'SalUnitMsr' => $request['SalUnitMsr'], // Sales UoM Name
                'NumInSale' => $request['NumInSale'], // Items Per Sales Unit
                'SalPackMsr' => $request['SalPackMsr'], //Packaging Uom Namep
                'SalPackUn' => $request['SalPackUn'], //Quantity Per Package

                'SVolume' => $request['SVolume'],
                'VatGourpSa' => $request['VatGourpSa'], //    Sales Tax Definition

                //inventry
                'EvalSystem' => $request['EvalSystem'],
                'GLMethod' => $request['GLMethod'],
                'InvntryUom' => $request['InvntryUom'], // Uom Name
                'IUoMEntry' => $request['IUoMEntry'], // Uom Code
                'CntUnitMsr' => $request['CntUnitMsr'],
                'NumInCnt' => $request['NumInCnt'],
                'INUoMEntry' => $request['INUoMEntry'],

                'DfltsGroup' => $request['DfltsGroup'],
                //Dimesnions
                'CogsOcrCodMthd' => $request['CogsOcrCodMthd'],
                'CogsOcrCo2Mthd' => $request['CogsOcrCo2Mthd'],
                'CogsOcrCo3Mthd' => $request['CogsOcrCo3Mthd'],
                'CogsOcrCo4Mthd' => $request['CogsOcrCo4Mthd'],
                'CogsOcrCo5Mthd' => $request['CogsOcrCo5Mthd'],
            ]);

            $priceListData = [
                'PriceList' => $request['PriceList'],
                'Price' => $request['Price'],
                'Currency' => $request['Currency'],
                'UomEntry' => $request['UomEntry'],
            ];

            (new InventoryService())->ItemMasterDataService($newItem->id, $priceListData);

            $warehoues = OWHS::get();
            foreach ($warehoues as $key => $value) {
                $itemPrice = OITW::create([
                    'ItemCode' => $newItem->id,
                    'WhsCode' => $value->id,
                    'UserSign' => $user->id,
                ]);
            }

            $IsArray1 = is_array($request['itm15']) ? 'Yes' : 'No';
            if ($IsArray1 == "Yes") {
                //Creating itm15[ item Properties]
                foreach ($request['itm15'] as $key => $value) {
                    $itemPrice = ITM15::create([
                        'ItemCode' => $newItem->id,
                        'ItmsTypCod' => $value['id'], //  ID propery e.g Size
                        'QryGroup' => array_key_exists('QryGroup', $value) ? $value['QryGroup'] : null, // ID property Desc e.G 45 METRES
                    ]);
                }
            }

            //Updating the NextNumber
            NumberingSeries::dispatch($request['Series']);
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
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
        $data = OITM::with('itm1.opln', 'inventoryuom', 'ougp.ugp1', 'oitb', 'oitw')
            ->where('id', $id)
            ->first();

        $WhsCode = \Request::get('WhseCode');
        $ObjType = \Request::get('ObjType');

        $availableSerial = OSRQ::where('ItemCode', $data->ItemCode)
            ->where('Quantity', 1)
            ->whereNull('CommitQty')
            ->where('WhsCode', $WhsCode)
            ->pluck('SysNumber');

        if ($ObjType == 14) {
            $availableSerial = OSRQ::where('ItemCode', $data->ItemCode)
                ->where('Quantity', 0)
                ->where('WhsCode', $WhsCode)
                ->pluck('SysNumber');
        }

        $data->osrn = OSRN::whereIn('SysNumber', $availableSerial)->where('ItemCode', $data->ItemCode)->get();

        $itm15 = ITM15::where('ItemCode', $id)->get();
        foreach ($itm15 as $key => $value) {

            $value->ItmsGrpNam = OITG::where('id', $value->ItmsTypCod)->value('ItmsGrpNam');
            $value->itg1 = ITG1::where('ItmsTypCod', $value->ItmsTypCod)->get();
        }
        $data->itm15 = $itm15;

        return (new ApiResponseService())->apiSuccessResponseService($data);
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
                'ItemCode' => $request['ItemCode'],
                'ItemName' => $request['ItemName'],
                'FrgnName' => $request['FrgnName'],
                'ItemType' => $request['ItemType'],
                'ItmsGrpCod' => $request['ItmsGrpCod'],
                'UgpEntry' => $request['UgpEntry'],
                'InvntItem' => $request['InvntItem'],
                'SellItem' => $request['SellItem'],
                'PrchseItem' => $request['PrchseItem'],

                //purchse
                'BuyUnitMsr' => $request['BuyUnitMsr'],
                'NumInBuy' => $request['NumInBuy'],
                'VatGroupPu' => $request['VatGroupPu'],

                //sales
                'SalUnitMsr' => $request['SalUnitMsr'],
                'NumInSale' => $request['NumInSale'],
                'VatGourpSa' => $request['VatGourpSa'],

                //inventry
                'ByWh' => $request['ByWh'],
                'EvalSystem' => $request['EvalSystem'],
                'GLMethod' => $request['GLMethod'],
                'InvntryUom' => $request['InvntryUom'],

                'DfltsGroup' => $request['DfltsGroup'],
                //Dimesnions
                'CogsOcrCodMthd' => $request['CogsOcrCodMthd'],
                'CogsOcrCo2Mthd' => $request['CogsOcrCo2Mthd'],
                'CogsOcrCo3Mthd' => $request['CogsOcrCo3Mthd'],
                'CogsOcrCo4Mthd' => $request['CogsOcrCo4Mthd'],
                'CogsOcrCo5Mthd' => $request['CogsOcrCo5Mthd'],
            ];
            OITM::where('id', $id)->update($details);
            // $Currency = OPLN::where('id', $request['PriceList'])->value($request['Currency']);
            // $itemPrices = [
            //     'PriceList' => $request['PriceList'],
            //     'Price' => $request['Price'],
            //     'Currency' => $Currency,
            //     'CurrencyType' => $request['Currency'],
            //     'BasePLNum' => $request['PriceList'],
            //     'UomEntry' => $request['UomEntry'],
            // ];
            // ITM1::where('id', $request['ITM1ID'])->update($itemPrices);

            return (new ApiResponseService())->apiSuccessResponseService("Saved");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Data
     */
    //    public function getItemUsingItemCode(string $ItemCode)
    //    {
    //        try {
    //            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
    //                ->with('salesuom', 'ougp')
    //                ->where('ItemCode', $ItemCode)
    //                ->first();
    //            return (new ApiResponseService())->apiSuccessResponseService($data);
    //        } catch (\Throwable $th) {
    //            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
    //        }
    //    }
    public function getItemUsingItemCode(Request $request)
    {
        try {
            //           $ItemCode = \Request::get('ItemCode');
            //            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
            //                ->with('salesuom', 'ougp')
            //                ->where('ItemCode', $ItemCode)
            //                ->first();
            //            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
            ////                ->with('salesuom', 'ougp')
            //                ->where('ItemCode', $ItemCode)
            //                ->first()->toArray();
            //get default price
            $data = PriceCalculationController::fetchItemDefaultPrice($request->all());
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
