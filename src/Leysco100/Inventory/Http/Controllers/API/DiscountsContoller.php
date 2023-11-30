<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Carbon\Carbon;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSPP;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP2;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SPP3;

class DiscountsContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OPLN::with('basenum', 'PrimCurr', 'addcurr1', 'addcurr2')->get();
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
            $data =  OSPP::create([
                "ItemCode" => $request['ItemCode'],
                "SrcPrice" => $request['SrcPrice'],
                "ValidFrom" => $request['ValidFrom'],
                "ValidTo" => $request['ValidTo'],
                "Valid" => $request['Valid'] ? "Y" : "N",
                "UserSign" => Auth::user()->id,
                "ListNum" => $request['ListNum']
            ]);
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



    public function getItemDiscount()
    {
        $ItemCode =  request()->filled('ItemCode') ? request()->input('ItemCode') : false;
        $ListNum =  request()->filled('ListNum') ? request()->input('ListNum') : false;
        $CardCode =  request()->filled('CardCode') ? request()->input('CardCode') : false;
        try {
            $ocrd = OCRD::where('CardCode', $CardCode)->select('CardCode', 'ListNum')->first();
            $ListNum = $ListNum ? $ListNum : $ocrd->ListNum;

            $today = Carbon::now()->toDateString();

            $ospp = OSPP::where('ListNum', $ListNum)->whereDate('ValidFrom', '<=', $today)
                ->where('ItemCode', $ItemCode)
                ->whereDate('ValidTo', '>=', $today)
                ->first();

            $DiscPrcnt = 0;
            $DiscType = 0;
            $spp3 = [];
            if ($ospp) {
                $spp1 = SPP1::where('LINENUM', $ospp->id)
                    ->whereDate('FromDate', '<=', $today)
                    ->whereDate('ToDate', '>=', $today)
                    ->where('ItemCode', $ItemCode)->latest()->first();

                if ($spp1) {
                    $DiscPrcnt =  $spp1->Discount;
                    $DiscType = 1;
                    $spp2 = SPP2::where('SPP1LNum', $spp1->id)->latest()->first();

                    if ($spp2) {
                        if ($spp2->DiscType == 1) {
                            $DiscPrcnt =  $spp2->Discount;
                        }
                        if ($spp2->DiscType == 2) {
                            $DiscType = 2;
                            $spp3 = SPP3::where('SPP2Num', $spp2->id)
                                ->with('item:id,ItemCode,ItemName,VatGourpSa,DfltWH')
                                ->select("id", "ItemCode", "CardCode", "SPP2Num", "Quantity", "Price")
                                ->get();
                        }
                    }
                }
            }

            $data = [
                "DiscPrcnt" => $DiscPrcnt,
                "DiscType" => $DiscType,
                "ForFree" =>   $spp3
            ];
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
