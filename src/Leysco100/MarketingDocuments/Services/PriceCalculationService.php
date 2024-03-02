<?php

namespace Leysco100\MarketingDocuments\Services;

use App\Domains\Finance\Models\OVTG;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;

class PriceCalculationService
{
    private $ItemCode;
    private $PriceList;
    private $SUoMEntry;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(string $ItemCode, int $PriceList, int $SUoMEntry)
    {
        $this->ItemCode = $ItemCode;
        $this->PriceList = $PriceList;
        $this->SUoMEntry = $SUoMEntry;
    }
    // GET NET DEFAULT PRICE
    public function getDefaultPrice()
    {
        try {
            $ITEM = OITM::where('ItemCode', $this->ItemCode)->first();

            $ugp1 = UGP1::where('UomEntry', $ITEM->SUoMEntry)
                ->where('UgpEntry', $ITEM->UgpEntry)
                ->first();
            $opln = OPLN::where('id', $this->PriceList)->first();
            $ovtg = TaxGroup::where('code', $ITEM->VatGourpSa)->first();
            // If there is a change on  sUOMTRY
            //This are OUOMS
            $PRICINGUNIT = $ITEM->PriceUnit; //IF OUM IS MANUAL THIS VALUE WILL BE NULL

            $SALESUNIT = $this->SUoMEntry;

            /**
             * Check IF there is a price in ITM9
             */

            $itm9 = ITM9::where('ItemCode', $ITEM->ItemCode)
                ->where('UomEntry', $SALESUNIT)
                ->where('PriceList', $this->PriceList)
                ->first();

            if ($itm9) {
                if ($opln->isGrossPrc == 'Y') {
                    return $itm9->Price;
                } else if ($opln->isGrossPrc == 'N') {
                    //RATE * PRICE (1 + RATE/100) * PRICE

                    return (($ovtg->rate / 100) + 1) *  $itm9->Price;
                }
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

            //Getting Current Price and Curreny
            $ITM1_DATA = ITM1::select('Price', 'Currency')
                ->where('ItemCode', $ITEM->ItemCode)
                ->where('PriceList', $opln->ExtRef)
                ->first();

            $PRICEPERPRICEUNIT = $ITM1_DATA->Price;

            $SALESUNITCONVERTEDTOBASEUOM = $SALESUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $SALESUNITCONVERTEDTOBASEUOM;

            $INVUNITCONVERTEDTOBASEUOM = $INVUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $INVUNITCONVERTEDTOBASEUOM;
            $PRICINGUNITCONVERTEDTOBASEUOM = $PRICINGUNITCONVERTEDTOBASEUOM == null || 0 ? 1 : $PRICINGUNITCONVERTEDTOBASEUOM;

            $PRICE = ($PRICEPERPRICEUNIT * $SALESUNITCONVERTEDTOBASEUOM) / $PRICINGUNITCONVERTEDTOBASEUOM;

            if ($opln->isGrossPrc == 'Y') {
                return  $PRICE;
            } else if ($opln->isGrossPrc == 'N') {
                //RATE * PRICE (1 + RATE/100) * PRICE
                return (($ovtg->rate / 100) + 1) *   $PRICE;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            return (new ApiResponseService())->apiMobileFailedResponseService($th->getMessage());
        }
    }
}
