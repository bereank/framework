<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;
use Leysco100\MarketingDocuments\Services\PriceCalculationService;


class MPricelistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OPLN::get();
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
            $data = OPLN::select('id', 'ListName')
                ->with([
                    'itm1' => function ($q) {
                        $q->select('id', 'ItemCode', 'PriceList', 'Price');
                        $q->with(['item' => function ($q) {
                            $q->select('id', 'ItemName', 'ItemCode');
                        }]);
                    }
                ])
                ->where('id', $id)->first();
            return $data;
        } catch (\Throwable $th) {
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }

    /**
     * Get ItemPrices
     *
     *
     */
    public function itemPrices()
    {

        return [];
        $updated_at = \Request::get('updated_at');

        $itemCodesItm1 = ITM1::where('updated_at', '>=', $updated_at)->pluck('ItemCode')->toArray();
        $itemCodesItm9 = ITM9::where('updated_at', '>=', $updated_at)->pluck('ItemCode')->toArray();

        $itemCodes = array_merge($itemCodesItm1, $itemCodesItm9);

        $allItemCodes = array_values(array_unique($itemCodes, SORT_REGULAR));

        $data = OITM::select('UgpEntry', 'ItemCode')->whereIn('ItemCode', $allItemCodes)
            ->paginate(10);

        $priceLists = OPLN::select('id')->whereIn('id', [2])->get();
        foreach ($data as $key => $item) {
            $item->makeHidden('full_name');
            $ugp1Data = UGP1::select('UomEntry')->where('UgpEntry', $item->UgpEntry)->get();

            $prices = [];
            foreach ($priceLists as $key => $pricelist) {
                foreach ($ugp1Data as $key => $ugp1) {
                    $Price = (new PriceCalculationService($item->ItemCode, $pricelist->id, $ugp1->UomEntry))
                        ->getDefaultPrice();
                    $priceDetails = [
                        "PriceList" => $pricelist->id,
                        "UomEntry" => $ugp1->UomEntry,
                        "Price" => $Price,
                    ];

                    array_push($prices, $priceDetails);
                }
            }

            $item->prices = $prices;

            unset($item->full_name);
        }

        return $data;
    }
}
