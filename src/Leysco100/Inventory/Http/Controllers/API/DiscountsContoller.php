<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Carbon\Carbon;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\EDG1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OEDG;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
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
            $validator = Validator::make($request->all(), [
                'ItemCode' => 'required',
                'ValidFrom' => 'required',
                'ValidTo' => 'required',
                'ListNum' => 'required',
            ]);
            $data =  OSPP::create([
                "ItemCode" => $request['ItemCode'],
                'LctCode' =>  $request['LctCode'] ?? null,
                "CardCode" => $request['CardCode'],
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



    public function getItemDiscount(Request $request)

    {


        try {
            $validatedData = $request->validate([
                "ItemCode" => 'required|exists:tenant.o_i_t_m_s',
                "CardCode" => 'required|exists:tenant.o_c_r_d_s',
                "Quantity" => "required"
            ]);
        } catch (ValidationException $e) {
            return (new ApiResponseService())->apiFailedResponseService($e->validator->errors()->toArray());
        }


        $ItemCode =  request()->filled('ItemCode') ? request()->input('ItemCode') : null;
        $ListNum =  request()->filled('ListNum') ? request()->input('ListNum') : null;
        $CardCode =  request()->filled('CardCode') ? request()->input('CardCode') : null;
        $Quantity =  request()->filled('Quantity') ? request()->input('Quantity') : 1;
        $UomEntry = request()->filled('UomEntry') ? request()->input('UomEntry') : false;
        try {
            $ocrd = OCRD::where('CardCode', $CardCode)->select('CardCode', 'ListNum')->first();
            $ListNum = $ListNum ? $ListNum : $ocrd->ListNum;

            $today = Carbon::now()->toDateString();
            $data = [];
            $spp3 = [];
            $DiscPrcnt = 0;
            $DiscType = 0;
            $discGrp = true;
            $items = [];
            $type = "period-discount";
            $oedg = OEDG::whereDate('ValidFrom', '<=', $today)
                ->whereDate('ValidTo', '>=', $today)
                ->where('Type', 'A')
                ->with(['edg1' => function ($query) use ($Quantity, $ItemCode) {
                    $query->with('item:id,ItemCode,ItemName,VatGourpSa,DfltWH')
                        ->where('PayFor', '<=', $Quantity)
                        ->where('ObjKey', $ItemCode)
                        ->select(
                            'DocEntry',
                            'ObjType',
                            'ObjKey',
                            'DiscType',
                            'Discount',
                            'PayFor',
                            'ForFree',
                            'UpTo',
                            'ForFree as Quantity'
                        );
                }])

                ->get();
            if ($oedg->count() > 0) {
                foreach ($oedg as $discountgroup) {
                    if ($discountgroup->edg1) {

                        if ($discountgroup->edg1->count() > 0) {
                            foreach ($discountgroup->edg1 as $edg1) {
                                if (($edg1->DiscType == 'P'  && $edg1->ObjType == 4)) {
                                    $discGrp = true;

                                    $data = [
                                        "DiscPrcnt" => $edg1->Discount,
                                        "DiscType" => 2,
                                        "ForFree" =>   $edg1,
                                        "Type" => "Discount-Group"
                                    ];
                                    return (new ApiResponseService())->apiSuccessResponseService($data);
                                } else {
                                    $discGrp = false;
                                }
                                if ($edg1->PayFor == 'D' && $edg1->ObjType == "4") {
                                    $discGrp = true;
                                    $data = [
                                        "DiscPrcnt" => $edg1->Discount,
                                        "DiscType" => 1,
                                        "ForFree" =>  [],
                                        "Type" => "Discount-Group"
                                    ];
                                    return (new ApiResponseService())->apiSuccessResponseService($data);
                                } else {
                                    $discGrp = false;
                                }
                            }
                        } else {
                            $discGrp = false;
                        }
                    } else {
                        $discGrp = false;
                    }
                }
            }
            if (!$discGrp) {
                $spp = OSPP::where('ListNum', $ListNum)->whereDate('ValidFrom', '<=', $today)
                    ->whereDate('ValidTo', '>=', $today)
                    ->where('ItemCode', $ItemCode)
                    ->where('CardCode', $CardCode)
                    ->where('Valid', 'Y')
                    ->get();
                if (!$spp) {
                    $spp = OSPP::where('ListNum', $ListNum)->whereDate('ValidFrom', '<=', $today)
                        ->whereDate('ValidTo', '>=', $today)
                        ->where('ItemCode', $ItemCode)
                        ->whereNull('CardCode')
                        ->where('Valid', 'Y')
                        ->get();
                }
                if ($spp) {
                    foreach ($spp as  $ospp) {
                        $spp1Data = SPP1::where('LINENUM', $ospp->id)
                            ->whereDate('FromDate', '<=', $today)
                            ->whereDate('ToDate', '>=', $today)

                            ->where('ItemCode', $ItemCode)->latest()->get();

                        if ($spp1Data) {
                            foreach ($spp1Data as $spp1) {
                                $DiscPrcnt =  $spp1->Discount;
                                $DiscType = 1;
                                $spp2 = SPP2::where('SPP1LNum', $spp1->id)
                                    ->when(!empty($UomEntry), function ($query) use ($UomEntry) {
                                        return $query->where('UomEntry', $UomEntry);
                                    })
                                    ->latest()->first();

                                if ($spp2) {
                                    if ($spp2->DiscType == 1) {
                                        $DiscPrcnt =  $spp2->Discount;
                                    }
                                    if ($spp2->DiscType == 2) {

                                        $DiscType = 2;
                                        $spp3 = SPP3::where('SPP2Num', $spp2->id)
                                            ->with('item:id,ItemCode,ItemName,VatGourpSa,DfltWH')
                                            ->select('id', 'Price', 'ItemCode', 'Quantity')
                                            ->get();
                                        $items = $spp3;
                                        $type = "volume-discount";
                                    }
                                }
                                $data = [
                                    "DiscPrcnt" => $DiscPrcnt,
                                    "DiscType" => $DiscType,
                                    "ForFree" =>   $items,
                                    "Type" => $type
                                ];
                                return (new ApiResponseService())->apiSuccessResponseService($data);
                            }
                        }
                    }
                }
            }
            $data = [
                "DiscPrcnt" => $DiscPrcnt,
                "DiscType" => $DiscType,
                "ForFree" =>   $items,
                "Type" => $type
            ];
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
