<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;

class PriceCalculationController extends Controller
{
    public function getDefaultPrice(Request $request)
    {
        try {
            $ITEM = OITM::with('taxgroup')->where('ItemCode', $request['ItemCode'])->first();
            $taxgroup = TaxGroup::where('code', $ITEM->VatGourpSa)->first();
            $ObjType = $request['ObjType'];

            /**
             *  If Purchase Request Service
             */
            if ($ObjType == 205) {
                $itm1 = ITM1::where('ItemCode', $ITEM->id)
                    ->first();
                $details = [
                    'FINALSALESPRICE' => $itm1 ? $itm1->Price : 0,
                ];

                return (new ApiResponseService())->apiSuccessResponseService($details);
            }

            /**
             * For Good Receipt,Good Issue and Inventory Transfer
             */
            if ($ObjType == 59 || $ObjType == 60 || $ObjType == 66 || $ObjType == 67) {
                $details = [
                    'FINALSALESPRICE' => 0,
                    'ItemDimensionDfts' => $this->getItemDefaultDimensions($ITEM->id),
                ];

                return (new ApiResponseService())->apiSuccessResponseService($details);
            }

            if (!isset($request['CardCode'])) {
                return (new ApiResponseService())->apiFailedResponseService("Customer is required");
            }

            $bpPriceList = OCRD::where('CardCode', $request['CardCode'])->value('ListNum');
            $priceList = OPLN::where('id', $bpPriceList)->first();

            $priceIsGross = false;

            $taxRate = ($taxgroup->rate / 100) + 1;
            if ($priceList->isGrossPrc == "Y" && $taxgroup->rate > 0) {
                $priceIsGross = true;
            }

            if (!$bpPriceList) {
                return (new ApiResponseService())->apiFailedResponseService("Customer does not have pricelist");
            }

            // If there is a change on  sUOMTRY
            //This are OUOMS
            $PRICINGUNIT = $ITEM->PriceUnit; //IF OUM IS MANUAL THIS VALUE WILL BE NULL
            if ($request['SUoMEntry']) {
                $SALESUNIT = $request['SUoMEntry'];
            } else {
                //Get Default Sales Unit
                $SALESUNIT = $ITEM->SUoMEntry;
            }

            //Get Inventor Uom
            $INVUNIT = $ITEM->IUoMEntry;
            //Getting PRICINGUNITCONVERTEDTOBASEUOM
            $PRICINGUNITCONVERTEDTOBASEUOM_QUERY = DB::table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as PRICINGUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $PRICINGUNIT)
                ->first();

            $PRICINGUNITCONVERTEDTOBASEUOM = $PRICINGUNITCONVERTEDTOBASEUOM_QUERY ? $PRICINGUNITCONVERTEDTOBASEUOM_QUERY->PRICINGUNITCONVERTEDTOBASEUOM : null;

            //Getting SALESUNITCONVERTEDTOBASEUOMu
            $SALESUNITCONVERTEDTOBASEUOM_QUERY = DB::table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as SALESUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $SALESUNIT)
                ->first();
            $SALESUNITCONVERTEDTOBASEUOM = $SALESUNITCONVERTEDTOBASEUOM_QUERY ? $SALESUNITCONVERTEDTOBASEUOM_QUERY->SALESUNITCONVERTEDTOBASEUOM : null;

            ////Getting INVUNITCONVERTEDTOBASEUOM
            $INVUNITCONVERTEDTOBASEUOM_QUERY = DB::table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as INVUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $INVUNIT)
                ->first();
            $INVUNITCONVERTEDTOBASEUOM = $INVUNITCONVERTEDTOBASEUOM_QUERY ? $INVUNITCONVERTEDTOBASEUOM_QUERY->INVUNITCONVERTEDTOBASEUOM : null;

            //Getting Current Price and Curreny
            $ITM1_DATA = ITM1::select('Price', 'Currency')
                ->where('ItemCode', $ITEM->ItemCode)
                ->where('PriceList', $priceList->ExtRef)
                ->first();
            if (!$ITM1_DATA) {
                $details = [
                    'FINALSALESPRICE' => 0,
                    "oitw" => OITW::where('ItemCode', $ITEM->ItemCode)->get(),
                    'ItemDimensionDfts' => $this->getItemDefaultDimensions($ITEM->id),
                ];
                return (new ApiResponseService())->apiSuccessResponseService($details);
            }

            /**
             * Check IF there is a price in ITM9
             */

            $itm9 = ITM9::where('ItemCode', $ITEM->ItemCode)
                ->where('UomEntry', $SALESUNIT)
                ->where('PriceList', $bpPriceList)
                ->first();

            if ($itm9) {
                $details = [
                    'ItemDimensionDfts' => $this->getItemDefaultDimensions($ITEM->id),
                    "oitw" => OITW::where('ItemCode', $ITEM->ItemCode)->get(),
                    'FINALSALESPRICE' => $priceIsGross ? $itm9->Price / $taxRate : $itm9->Price,
                ];
                return (new ApiResponseService())->apiSuccessResponseService($details);
            }

            $PRICEPERPRICEUNIT = $ITM1_DATA->Price;
            $PRICINGCURRENCY = $ITM1_DATA->Currency;

            $SALESUNITCONVERTEDTOBASEUOM = $SALESUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $SALESUNITCONVERTEDTOBASEUOM;
            $INVUNITCONVERTEDTOBASEUOM = $INVUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $INVUNITCONVERTEDTOBASEUOM;
            $PRICINGUNITCONVERTEDTOBASEUOM = $PRICINGUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $PRICINGUNITCONVERTEDTOBASEUOM;

            $FINALSALESPRICE = ($PRICEPERPRICEUNIT * $SALESUNITCONVERTEDTOBASEUOM) / $PRICINGUNITCONVERTEDTOBASEUOM;

            $details = [
                'SalesUnitINVUnitConversion' => $SALESUNITCONVERTEDTOBASEUOM / $INVUNITCONVERTEDTOBASEUOM,
                'SalesUnitPriceUnitConversion' => $SALESUNITCONVERTEDTOBASEUOM / $PRICINGUNITCONVERTEDTOBASEUOM,
                'PRICEPERPRICEUNIT' => $PRICEPERPRICEUNIT,
                'PRICINGCURRENCY' => $PRICINGCURRENCY,
                'ItemDimensionDfts' => $this->getItemDefaultDimensions($ITEM->id),
                "oitw" => OITW::where('ItemCode', $ITEM->ItemCode)->get(),
                'FINALSALESPRICE' => $priceIsGross ? $FINALSALESPRICE / $taxRate : $FINALSALESPRICE,
            ];

            return (new ApiResponseService())->apiSuccessResponseService($details);
        } catch (\Throwable$th) {
            info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public static function fetchItemDefaultPrice($request)
    {
        try {

//            $data = OITM::select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
////                ->with('salesuom', 'ougp')
//                ->where('ItemCode', $ItemCode)
//                ->first()->toArray();

            $itemDefaultDimmension = new PriceCalculationController();
            if($request['type'])
            if(strtolower($request['type'])=='defaults')
            {
                if(!$request['variable'])
                    return "approprriate variable must be filled";
;
                $ITEMDATA['ItemDimensionDfts'] = $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$request['variable']);
                return  $ITEMDATA;
            }

            $ObjType = $request['ObjType'];

             /*  If Purchase Request Service
             */
            if ($ObjType == 205) {
                $ITEM = DB::connection("tenant")->table('o_i_t_m_s')
                    ->select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61','PriceUnit')
                    ->where('ItemCode', $request['ItemCode'])->first()->toArray();
                $itm1 = DB::connection("tenant")->table('i_t_m1_s')->where('ItemCode', $request['ItemCode'])->select('Price')
                    ->first();
                //$details = [
                 //   'FINALSALESPRICE' => $itm1 ? $itm1->Price : 0,
               // ];
                $ITEM['FINALSALESPRICE']=$itm1 ? $itm1->Price : 0;
                return  $ITEM;
            }

            /**
             * For Good Receipt,Good Issue and Inventory Transfer
             */
            if ($ObjType == 59 || $ObjType == 60 || $ObjType == 66 || $ObjType == 67) {
                $ITEM = DB::connection("tenant")->table('o_i_t_m_s')
                    ->select('id', 'ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61','PriceUnit')
                    ->where('ItemCode',$request['ItemCode'])->first();
                /*$details = [
                    'FINALSALESPRICE' => 0,
                    'ItemDimensionDfts' => $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61),
                ];*/
                $ITEM['FINALSALESPRICE'] = 0;
                if($request['type'])
                if(!(strtolower($request['type'])=='pricedata'))
                    $ITEM['ItemDimensionDfts']=$itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61);


                return $ITEM;
            }

            if (!isset($request['CardCode'])) {
                return "Customer is required";
            }

            $bpPriceList = DB::connection("tenant")->table('o_c_r_d_s')->where('CardCode', $request['CardCode'])->value('ListNum');
            if (!$bpPriceList) {
                return "Customer does not have pricelist";
            }
            $priceList = DB::connection("tenant")->table('o_p_l_n_s')->where('id', $bpPriceList)->select('isGrossPrc','id','ExtRef')->first();

            $priceIsGross = false;





            //Getting UNITCONVERTEDTOBASEUOMu
            $ITEM =DB::connection("tenant")->table('o_i_t_m_s')
               ->leftJoin('u_g_p1_s AS u_g_p1prcngunit', function ($join) use($request) {
                    ///$join->on('o_i_t_m_s.ItemCode', '=',DB::raw("'".$ItemCode."'"));
                    $join->where('ItemCode', $request['ItemCode']);
                    $join->on('o_i_t_m_s.UgpEntry', '=', 'u_g_p1prcngunit.UgpEntry');
                    $join->on('o_i_t_m_s.PriceUnit', '=','u_g_p1prcngunit.UomEntry');
                })

                ->leftJoin('u_g_p1_s AS u_g_p1salsunit', function ($join) use($request) {
                    ///$join->on('o_i_t_m_s.ItemCode', '=',DB::raw("'".$ItemCode."'"));
                    $join->where('ItemCode', $request['ItemCode']);
                    $join->on('o_i_t_m_s.UgpEntry', '=', 'u_g_p1salsunit.UgpEntry');
                    if ($request['SUoMEntry']) {
                        $join->where('u_g_p1salsunit.UomEntry',$request['SUoMEntry']);
                    } else {
                        //Get Default Sales Unit
                        $join->on('o_i_t_m_s.SUoMEntry','=','u_g_p1salsunit.UomEntry');
                    }
                })
                ->leftJoin('u_g_p1_s AS u_g_p1invunit', function ($join) use($request) {
                    $join->where('ItemCode', $request['ItemCode']);
                    ///$join->on('o_i_t_m_s.ItemCode', '=',DB::raw("'".$ItemCode."'"));
                    $join->on('o_i_t_m_s.UgpEntry', '=', 'u_g_p1invunit.UgpEntry');
                    $join->on('o_i_t_m_s.INUoMEntry','=','u_g_p1invunit.UomEntry');
                })
                ->select('o_i_t_m_s.INUoMEntry','o_i_t_m_s.id','o_i_t_m_s.ItemCode','o_i_t_m_s.SUoMEntry', 'o_i_t_m_s.ItemName',  'o_i_t_m_s.PUoMEntry', 'o_i_t_m_s.UgpEntry', 'o_i_t_m_s.VatGourpSa', 'o_i_t_m_s.VatGroupPu', 'o_i_t_m_s.ManSerNum', 'o_i_t_m_s.QryGroup61','o_i_t_m_s.PriceUnit')
                ->selectRaw('u_g_p1salsunit.BaseQty/u_g_p1salsunit.AltQty as SALESUNITCONVERTEDTOBASEUOM')
                ->selectRaw('u_g_p1invunit.BaseQty/u_g_p1invunit.AltQty as INVUNITCONVERTEDTOBASEUOM')
                ->selectRaw('u_g_p1prcngunit.BaseQty/u_g_p1prcngunit.AltQty as PRICINGUNITCONVERTEDTOBASEUOM')
                ->where('ItemCode', $request['ItemCode'])


                //->whereRaw('o_i_t_m_s.ItemCode=\''.$ItemCode.'\'');;
              ->first();


            //$query = vsprintf(str_replace(array('?'), array('\'%s\''), $ITEM->toSql()), $ITEM->getBindings()); dd($query);

            $ITEMDATA = [
                'id'=>$ITEM->id, 'ItemName'=>$ITEM->ItemName, 'ItemCode'=>$ITEM->ItemCode, 'INUoMEntry'=>$ITEM->INUoMEntry, 'SUoMEntry'=>$ITEM->SUoMEntry, 'PUoMEntry'=>$ITEM->PUoMEntry, 'UgpEntry'=>$ITEM->UgpEntry, 'VatGourpSa'=>$ITEM->VatGourpSa, 'VatGroupPu'=>$ITEM->VatGroupPu, 'ManSerNum'=>$ITEM->ManSerNum, 'QryGroup61'=>$ITEM->QryGroup61,'PriceUnit'=>$ITEM->PriceUnit
            ];

            $taxgroup = TaxGroup::where('code',  $ITEM->VatGourpSa)->selectRaw('rate')->first();
            $taxRate = ($taxgroup->rate / 100) + 1;
            if ($priceList->isGrossPrc == "Y" && $taxgroup->rate > 0) {
                $priceIsGross = true;
            }

            //Getting Current Price and Curreny
            $ITM1_DATA = DB::connection("tenant")->table('i_t_m1_s')->select('Price', 'Currency')
                ->where('ItemCode', $request['ItemCode'])
                ->where('PriceList', $priceList->ExtRef)
                ->first();

            if (!$ITM1_DATA) {
                /*$details = [
                    'FINALSALESPRICE' => 0,
                    "oitw" => DB::table('o_i_t_w_s')->where('ItemCode',  $request['ItemCode'])->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get(),
                    'ItemDimensionDfts' => $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61),
                ];*/
                $ITEMDATA['FINALSALESPRICE']=0;
                $ITEMDATA['oitw']= DB::connection("tenant")->table('o_i_t_w_s')->where('ItemCode',  $request['ItemCode'])->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get();
                if($request['type'])
                if(!(strtolower($request['type'])=='pricedata'))
                $ITEMDATA['ItemDimensionDfts']=$itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61);;
                return $ITEMDATA;
            }

            /**
             * Check IF there is a price in ITM9
             */

            $itm9 =  DB::connection("tenant")->table('i_t_m9_s')->where('ItemCode', $ITEM->ItemCode)
                ->select('Price')
                ->where('UomEntry', $ITEM->SUoMEntry)
                ->where('PriceList', $bpPriceList)
                ->first();

            if ($itm9) {
                /*$details = [
                    'ItemDimensionDfts' => $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61),
                    "oitw" => DB::table('o_i_t_w_s')->where('ItemCode',  $request['ItemCode'])->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get(),
                    'FINALSALESPRICE' => $priceIsGross ? $itm9->Price / $taxRate : $itm9->Price,
                ];*/

                $ITEMDATA['FINALSALESPRICE']=$priceIsGross ? $itm9->Price / $taxRate : $itm9->Price;
                $ITEMDATA['oitw']= DB::connection("tenant")->table('o_i_t_w_s')->where('ItemCode',  $request['ItemCode'])->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get();
                if($request['type'])
                if(!(strtolower($request['type'])=='pricedata'))
                $ITEMDATA['ItemDimensionDfts']=$itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61);;


                return $ITEMDATA;
            }

            $PRICEPERPRICEUNIT = $ITM1_DATA->Price;
            $PRICINGCURRENCY = $ITM1_DATA->Currency;

            $SALESUNITCONVERTEDTOBASEUOM = $ITEM->SALESUNITCONVERTEDTOBASEUOM==null ||  $ITEM->SALESUNITCONVERTEDTOBASEUOM==0 ? 1 : $ITEM->SALESUNITCONVERTEDTOBASEUOM;
            $INVUNITCONVERTEDTOBASEUOM =$ITEM->INVUNITCONVERTEDTOBASEUOM==null ||  $ITEM->INVUNITCONVERTEDTOBASEUOM==0? 1 : $ITEM->INVUNITCONVERTEDTOBASEUOM;
            $PRICINGUNITCONVERTEDTOBASEUOM = $ITEM->PRICINGUNITCONVERTEDTOBASEUOM==null ||  $ITEM->PRICINGUNITCONVERTEDTOBASEUOM==0 ? 1 : $ITEM->PRICINGUNITCONVERTEDTOBASEUOM;

            $FINALSALESPRICE = ($PRICEPERPRICEUNIT * $SALESUNITCONVERTEDTOBASEUOM) / $PRICINGUNITCONVERTEDTOBASEUOM;

            /**$details = [
                'SalesUnitINVUnitConversion' => $SALESUNITCONVERTEDTOBASEUOM / $INVUNITCONVERTEDTOBASEUOM,
                'SalesUnitPriceUnitConversion' => $SALESUNITCONVERTEDTOBASEUOM / $PRICINGUNITCONVERTEDTOBASEUOM,
                'PRICEPERPRICEUNIT' => $PRICEPERPRICEUNIT,
                'PRICINGCURRENCY' => $PRICINGCURRENCY,
                'ItemDimensionDfts' => $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61),
                "oitw" => DB::table('o_i_t_w_s')->where('ItemCode', $ITEM->ItemCode)->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get(),
                'FINALSALESPRICE' => $priceIsGross ? $FINALSALESPRICE / $taxRate : $FINALSALESPRICE,
            ];*/

               $ITEMDATA['SalesUnitINVUnitConversion'] = $SALESUNITCONVERTEDTOBASEUOM / $INVUNITCONVERTEDTOBASEUOM;
                $ITEMDATA['SalesUnitPriceUnitConversion'] = $SALESUNITCONVERTEDTOBASEUOM / $PRICINGUNITCONVERTEDTOBASEUOM;
                $ITEMDATA['PRICEPERPRICEUNIT'] = $PRICEPERPRICEUNIT;
                $ITEMDATA['PRICINGCURRENCY'] = $PRICINGCURRENCY;
                $ITEMDATA['U_AllowDisc'] = $ITEM->QryGroup61 == "Y" ? "N" : "Y";;
                $ITEMDATA["oitw"] = DB::connection("tenant")->table('o_i_t_w_s')->where('ItemCode', $ITEM->ItemCode)->selectRaw('id,WhsCode,ItemCode,OnHand,OnOrder,IsCommited,AvgPrice')->get();
                $ITEMDATA['FINALSALESPRICE'] = $priceIsGross ? $FINALSALESPRICE / $taxRate : $FINALSALESPRICE;

                if($request['type'])
                if(!(strtolower($request['type'])=='pricedata'))
                $ITEMDATA['ItemDimensionDfts'] = $itemDefaultDimmension->getdefiniteItemDefaultsDimensions( $request['ItemCode'],$ITEM->QryGroup61);





            return $ITEMDATA;
        } catch (\Throwable$th) {
            info($th);
            return $th->getMessage();
        }
    }

    public function getItemDefaultDimensions($itemID)
    {
//        return null;
        $item = OITM::with('oidg')->where('id', $itemID)->first();
        $oudg = Auth::user()->oudg;
        $getOcrCode2 = DB::select('call DEPARTMENT_AUTOCOGS(?)', array($item->ItemCode));
        $getOcrCode3 = DB::select('call PRODUCTLINE_AUTOCOGS(?)', array($item->ItemCode));

        return [
            'OcrCode' => $oudg->CogsOcrCod,
            'OcrCode2' => $getOcrCode2[0]->OcrCode2,
            'OcrCode3' => $getOcrCode3[0]->OcrCode3,
            'U_AllowDisc' => $item->QryGroup61 == "Y" ? "N" : "Y",
        ];
    }


    public function getdefiniteItemDefaultsDimensions($ItemCode,$QryGroup61)
    {
//        return null;
        //$item = OITM::with('oidg')->where('id', $itemID)->select()->first();
        $oudg = Auth::user()->oudg;
        $getOcrCode2 = DB::select('call DEPARTMENT_AUTOCOGS(?)', array($ItemCode));
        $getOcrCode3 = DB::select('call PRODUCTLINE_AUTOCOGS(?)', array($ItemCode));

        return [
            'OcrCode' => $oudg->CogsOcrCod,
            'OcrCode2' => $getOcrCode2[0]->OcrCode2,
            'OcrCode3' => $getOcrCode3[0]->OcrCode3,
            'U_AllowDisc' => $QryGroup61 == "Y" ? "N" : "Y",
        ];
    }

}
