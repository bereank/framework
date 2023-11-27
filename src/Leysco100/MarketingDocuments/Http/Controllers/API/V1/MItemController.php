<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ITG1;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITG;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM15;



class MItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $search = \Request::get('filter');
        $all = \Request::get('all');
        $data = OITM::select('id', 'ItemName', 'ItemCode', 'UgpEntry',
         'SUoMEntry','VatGourpSa', 'OnHand','frozenFor')
            ->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('ItemCode', 'LIKE', "%$search%")
                        ->orWhere('ItemName', 'LIKE', "%$search%");
                }
            })
            ->when(!$all, function ($query) {
               $query->where('frozenFor', "N")
                ->where('OnHand', '>', 0);
            })  
            ->get();
        return $data;
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
            $data = OITM::with('itm1.opln', 'inventoryuom', 'ougp.ugp1.uom', 'oitb', 'oitw')
                ->where('id', $id)
                ->first();

            $itm15 = ITM15::where('ItemCode', $data->id)->get();
            foreach ($itm15 as $key => $value) {
                $value->ItmsGrpNam = OITG::where('id', $value->ItmsTypCod)->value('ItmsGrpNam');
                $value->itg1 = ITG1::where('ItmsTypCod', $value->ItmsTypCod)->get();
            }
            $data->itm15 = $itm15;

            return $data;
        } catch (\Throwable $th) {
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }

    /**
     *
     * For Getting Unit of Measure Unit of Measure group assigned to Product
     */

    public function getUnitOfMeasure($ougID)
    {
        try {
            $data = UGP1::with('uomentry')
                ->where('UgpEntry', $ougID)
                ->get();
            return $data;
        } catch (\Throwable $th) {
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }

    public function getDefaultPrice(Request $request)
    {
        $this->validate($request, [
            'ObjType' => 'required',
            'ItemCode' => 'required',
        ]);

        try {
            $ObjType = $request['ObjType'];

            $ITEM = OITM::findOrFail($request['ItemCode']);

            $ugp1 = UGP1::where('UomEntry', $ITEM->SUoMEntry)
                ->where('UgpEntry', $ITEM->UgpEntry)
                ->first();

            //Getting Pricing Unit From OITM

            //Checkig if Price list for Item Exist

            $bpPriceList = OCRD::where('id', $request['CardCode'])->value('ListNum');
            if (!$bpPriceList) {
                return (new ApiResponseService())
                    ->apiMobileFailedResponseService("Customer does not have pricelist");
            }

            // If there is a change on  sUOMTRY
            //This are OUOMS
            $PRICINGUNIT = $ITEM->PriceUnit; //IF OUM IS MANUAL THIS VALUE WILL BE NULL

            if ($request['SUoMEntry']) {
                $SALESUNIT = UGP1::where('id', $request['SUoMEntry'])->value('UomEntry');
            } else {
                //Get Default Sales Unit
                $SALESUNIT = $ITEM->SUoMEntry;
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
                    "SUoMEntry" => $ugp1 ? $ugp1->id : null,
                    'FINALSALESPRICE' => $itm9->Price,
                ];
                return $details;
            }

            //Get Inventor Uom
            $INVUNIT = $ITEM->IUoMEntry;
            //Getting PRICINGUNITCONVERTEDTOBASEUOM
            $PRICINGUNITCONVERTEDTOBASEUOM_QUERY = DB::connection("tenant")->table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as PRICINGUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $PRICINGUNIT)
                ->first();

            $PRICINGUNITCONVERTEDTOBASEUOM = $PRICINGUNITCONVERTEDTOBASEUOM_QUERY ? $PRICINGUNITCONVERTEDTOBASEUOM_QUERY->PRICINGUNITCONVERTEDTOBASEUOM : null;

            //Getting SALESUNITCONVERTEDTOBASEUOMu
            $SALESUNITCONVERTEDTOBASEUOM_QUERY = DB::connection("tenant")->table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as SALESUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $SALESUNIT)
                ->first();
            $SALESUNITCONVERTEDTOBASEUOM = $SALESUNITCONVERTEDTOBASEUOM_QUERY ? $SALESUNITCONVERTEDTOBASEUOM_QUERY->SALESUNITCONVERTEDTOBASEUOM : null;

            //Getting INVUNITCONVERTEDTOBASEUOM
            $INVUNITCONVERTEDTOBASEUOM_QUERY = DB::connection("tenant")->table('o_i_t_m_s')
                ->join('u_g_p1_s', 'o_i_t_m_s.UgpEntry', '=', 'u_g_p1_s.UgpEntry')
                ->selectRaw('u_g_p1_s.BaseQty,AltQty, BaseQty/AltQty as INVUNITCONVERTEDTOBASEUOM')
                ->where('o_i_t_m_s.id', $ITEM->id)
                ->where('u_g_p1_s.UomEntry', $INVUNIT)
                ->first();
            $INVUNITCONVERTEDTOBASEUOM = $INVUNITCONVERTEDTOBASEUOM_QUERY ? $INVUNITCONVERTEDTOBASEUOM_QUERY->INVUNITCONVERTEDTOBASEUOM : null;

            $opln = OPLN::where('id', $bpPriceList)->first();

            //Getting Current Price and Curreny
            $ITM1_DATA = ITM1::select('Price', 'Currency')
                ->where('ItemCode', $ITEM->ItemCode)
                ->where('PriceList', $opln->ExtRef)
                ->first();

            $PRICEPERPRICEUNIT = $ITM1_DATA->Price;
            $PRICINGCURRENCY = $ITM1_DATA->Currency;

            $SALESUNITCONVERTEDTOBASEUOM = $SALESUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $SALESUNITCONVERTEDTOBASEUOM;

            $INVUNITCONVERTEDTOBASEUOM = $INVUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $INVUNITCONVERTEDTOBASEUOM;
            $PRICINGUNITCONVERTEDTOBASEUOM = $PRICINGUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $PRICINGUNITCONVERTEDTOBASEUOM;

            $details = [
                "SUoMEntry" => $ugp1 ? $ugp1->id : null,
                'FINALSALESPRICE' => ($PRICEPERPRICEUNIT * $SALESUNITCONVERTEDTOBASEUOM) / $PRICINGUNITCONVERTEDTOBASEUOM,
            ];

            return $details;
        } catch (\Throwable $th) {
            Log::error($th);
            return (new ApiResponseService())->apiMobileFailedResponseService($th->getMessage());
        }
    }

    /**
     *
     * For Getting Unit of Measure Unit of Measure group assigned to Product
     */

    public function getAllUnitOfMeasure()
    {
        try {
            $data = UGP1::select('id', 'UomEntry', 'UgpEntry')
                ->with('uom:id,UomCode,UomName')
                ->get();

            return $data;
        } catch (\Throwable $th) {
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }
}
