<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Jobs\PriceUpdateJob;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\OUGP;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM9;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITB;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\ITM12;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updated_at_gteq = \Request::get('updated_at_gteq');

        $date = Carbon::parse($updated_at_gteq)->toDateTimeString();

        try {
            $data = OITM::select('ItemName', 'ItemCode', 'INUoMEntry', 'SUoMEntry', 'PUoMEntry', 'UgpEntry', 'VatGourpSa', 'VatGroupPu', 'ManSerNum', 'QryGroup61')
                ->where('updated_at', '>=', $date)
                ->paginate(500);
            return $data;
        } catch (\Throwable $th) {
            return $th;
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
        $defaultSettings = OADM::where('id', 1)->first();
        $itemGroup = OITB::where('ItmsGrpCod', $request['ItmsGrpCod'])->first();

        if (!$itemGroup) {
            info("--------------------------------Item Group Does not Exist-------------------------------------");
            info($request);
            return (new ApiResponseService())->apiIntegratorFailedResponseService("Item Group doest not exist", 401, "Item Group doest not exist");
        }

        try {
            $data = OITM::firstOrcreate([
                'ItemCode' => $request['ItemCode'],
            ], [
                'ItemName' => $request['ItemName'],
                'ExtRef' => $request['ExtRef'],
                'ItmsGrpCod' => $itemGroup->id,
                'PriceUnit' => $this->searchUOM($request['PriceUnit']),
                'UgpEntry' => $request['UgpEntry'],
                'VatGourpSa' => $request['VatGourpSa'] ? $request['VatGourpSa'] : $defaultSettings->DfSVatItem,
                'VatGroupPu' => $request['VatGroupPu'] ? $request['VatGroupPu'] : $defaultSettings->DfPVatItem,
                'SUoMEntry' => $this->searchUOM($request['SUoMEntry']), //Sales UoM Code
                'IUoMEntry' => $this->searchUOM($request['IUoMEntry']),
                'PUoMEntry' => $this->searchUOM($request['PUoMEntry']),
                'ManBtchNum' => $request['ManBtchNum'],
                'ManSerNum' => $request['ManSerNum'],
                'SWeight1' => $request['SWeight1'],
                'SWght1Unit' => $request['SWght1Unit'],
                'SWeight2' => $request['SWeight2'],
                'SWght2Unit' => $request['SWght2Unit'],
                'DfltsGroup' => 1,
                'CogsOcrCodMthd' => "U",
                'CogsOcrCo2Mthd' => "L",
                'CogsOcrCo3Mthd' => "L",
                'CogsOcrCo4Mthd' => "L",
                'CogsOcrCo5Mthd' => "U",
                'U_ProdLine' => $request['U_ProdLine'],
                'U_GrpDesc' => $request['U_GrpDesc'],
                "QryGroup1" => $request['QryGroup1'],
                "QryGroup2" => $request['QryGroup2'],
                "QryGroup3" => $request['QryGroup3'],
                "QryGroup4" => $request['QryGroup4'],
                "QryGroup5" => $request['QryGroup5'],
                "QryGroup6" => $request['QryGroup6'],
                "QryGroup7" => $request['QryGroup7'],
                "QryGroup8" => $request['QryGroup8'],
                "QryGroup9" => $request['QryGroup9'],
                "QryGroup10" => $request['QryGroup10'],
                "QryGroup11" => $request['QryGroup11'],
                "QryGroup12" => $request['QryGroup12'],
                "QryGroup13" => $request['QryGroup13'],
                "QryGroup14" => $request['QryGroup14'],
                "QryGroup15" => $request['QryGroup15'],
                "QryGroup16" => $request['QryGroup16'],
                "QryGroup17" => $request['QryGroup17'],
                "QryGroup18" => $request['QryGroup18'],
                "QryGroup19" => $request['QryGroup19'],
                "QryGroup20" => $request['QryGroup20'],
                "QryGroup21" => $request['QryGroup21'],
                "QryGroup22" => $request['QryGroup22'],
                "QryGroup23" => $request['QryGroup23'],
                "QryGroup24" => $request['QryGroup24'],
                "QryGroup25" => $request['QryGroup25'],
                "QryGroup26" => $request['QryGroup26'],
                "QryGroup27" => $request['QryGroup27'],
                "QryGroup28" => $request['QryGroup28'],
                "QryGroup29" => $request['QryGroup29'],
                "QryGroup30" => $request['QryGroup30'],
                "QryGroup31" => $request['QryGroup31'],
                "QryGroup32" => $request['QryGroup32'],
                "QryGroup33" => $request['QryGroup33'],
                "QryGroup34" => $request['QryGroup34'],
                "QryGroup35" => $request['QryGroup35'],
                "QryGroup36" => $request['QryGroup36'],
                "QryGroup37" => $request['QryGroup37'],
                "QryGroup38" => $request['QryGroup38'],
                "QryGroup39" => $request['QryGroup39'],
                "QryGroup40" => $request['QryGroup40'],
                "QryGroup41" => $request['QryGroup41'],
                "QryGroup42" => $request['QryGroup42'],
                "QryGroup43" => $request['QryGroup43'],
                "QryGroup44" => $request['QryGroup44'],
                "QryGroup45" => $request['QryGroup45'],
                "QryGroup46" => $request['QryGroup46'],
                "QryGroup47" => $request['QryGroup47'],
                "QryGroup48" => $request['QryGroup48'],
                "QryGroup49" => $request['QryGroup49'],
                "QryGroup50" => $request['QryGroup50'],
                "QryGroup51" => $request['QryGroup51'],
                "QryGroup52" => $request['QryGroup52'],
                "QryGroup53" => $request['QryGroup53'],
                "QryGroup54" => $request['QryGroup54'],
                "QryGroup55" => $request['QryGroup55'],
                "QryGroup56" => $request['QryGroup56'],
                "QryGroup57" => $request['QryGroup57'],
                "QryGroup58" => $request['QryGroup58'],
                "QryGroup59" => $request['QryGroup59'],
                "QryGroup60" => $request['QryGroup60'],
                "QryGroup61" => $request['QryGroup61'],
                "QryGroup62" => $request['QryGroup62'],
                "QryGroup63" => $request['QryGroup63'],
                "QryGroup64" => $request['QryGroup64'],
                "PrchseItem" => $request['PrchseItem'],
                "SellItem" => $request['SellItem'],
                "InvntItem" => $request['InvntItem'],
                "frozenFor" => $request['frozenFor'],
            ]);

            info("ITEM CREATED: " . $data->id);

            if (count($request['itemSalesUnitOfMeasurements']) > 0) {
                info($request['itemSalesUnitOfMeasurements']);
                ITM12::where('ItemCode', $request['ItemCode'])->delete();
                foreach ($request['itemSalesUnitOfMeasurements'] as $key => $value) {

                    ITM12::create([
                        'ItemCode' => $request['ItemCode'],
                        'UomType' => $value['UomType'] ?? null,
                        'UomEntry' => $value['UomEntry'] ?? null,
                    ]);
                }
            }

            return $data;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            info($th);
            return (new ApiResponseService())->apiIntegratorFailedResponseService($th->getMessage(), -1, "Unkown");
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
            $data = OITM::where('id', $id)->first();
            $salesUom = OUOM::where('id', $data->SUoMEntry)->first();
            $code = $data->ItemCode;
            $data->code = $code;
            $data->SUoMEntry = $salesUom ? $salesUom->ExtRef : null;
            return $data;
        } catch (\Throwable $th) {
            return $th;
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
        try {
            $defaultSettings = OADM::where('id', 1)->first();
            $data = OITM::where('id', $id)->first();

            if (!$data) {
                info("-------------------------------ITEM NOT FOUND:---------------------------------------------------");
                info($request);
                return (new ApiResponseService())->apiIntegratorFailedResponseService("Item Not Found : " . $request['ItemCode'], -1, "Unkown");
            }

            $itemGroup = OITB::where('ItmsGrpCod', $request['ItmsGrpCod'])->first();

            if (!$itemGroup) {
                info("------------------------------ITEM GROUP NOT FOUND:---------------------------------------------------");
                info($request);
                return (new ApiResponseService())
                    ->apiIntegratorFailedResponseService("Item Group doest not exist", 401, "Item Group doest not exist");
            }

            $data->update([
                'ItemCode' => $request['ItemCode'],
                'ItemName' => $request['ItemName'],
                'ExtRef' => $request['ExtRef'],
                'PriceUnit' => $this->searchUOM($request['PriceUnit']),
                'ItmsGrpCod' => $itemGroup->id,
                'VatGourpSa' => $request['VatGourpSa'] ? $request['VatGourpSa'] : $defaultSettings->DfSVatItem,
                'VatGroupPu' => $request['VatGroupPu'] ? $request['VatGroupPu'] : $defaultSettings->DfPVatItem,
                'UgpEntry' => $request['UgpEntry'],
                'SUoMEntry' => $this->searchUOM($request['SUoMEntry']), //Sales UoM Code
                'IUoMEntry' => $this->searchUOM($request['IUoMEntry']),
                'PUoMEntry' => $this->searchUOM($request['PUoMEntry']),
                'ManBtchNum' => $request['ManBtchNum'],
                'ManSerNum' => $request['ManSerNum'],
                'SWeight1' => $request['SWeight1'],
                'SWght1Unit' => $request['SWght1Unit'],
                'SWeight2' => $request['SWeight2'],
                'SWght2Unit' => $request['SWght2Unit'],
                'CogsOcrCodMthd' => "U",
                'CogsOcrCo2Mthd' => "L",
                'CogsOcrCo3Mthd' => "L",
                'CogsOcrCo4Mthd' => "L",
                'CogsOcrCo5Mthd' => "U",
                'DfltsGroup' => 1,
                'U_ProdLine' => $request['U_ProdLine'],
                'U_GrpDesc' => $request['U_GrpDesc'],
                "QryGroup1" => $request['QryGroup1'],
                "QryGroup2" => $request['QryGroup2'],
                "QryGroup3" => $request['QryGroup3'],
                "QryGroup4" => $request['QryGroup4'],
                "QryGroup5" => $request['QryGroup5'],
                "QryGroup6" => $request['QryGroup6'],
                "QryGroup7" => $request['QryGroup7'],
                "QryGroup8" => $request['QryGroup8'],
                "QryGroup9" => $request['QryGroup9'],
                "QryGroup10" => $request['QryGroup10'],
                "QryGroup11" => $request['QryGroup11'],
                "QryGroup12" => $request['QryGroup12'],
                "QryGroup13" => $request['QryGroup13'],
                "QryGroup14" => $request['QryGroup14'],
                "QryGroup15" => $request['QryGroup15'],
                "QryGroup16" => $request['QryGroup16'],
                "QryGroup17" => $request['QryGroup17'],
                "QryGroup18" => $request['QryGroup18'],
                "QryGroup19" => $request['QryGroup19'],
                "QryGroup20" => $request['QryGroup20'],
                "QryGroup21" => $request['QryGroup21'],
                "QryGroup22" => $request['QryGroup22'],
                "QryGroup23" => $request['QryGroup23'],
                "QryGroup24" => $request['QryGroup24'],
                "QryGroup25" => $request['QryGroup25'],
                "QryGroup26" => $request['QryGroup26'],
                "QryGroup27" => $request['QryGroup27'],
                "QryGroup28" => $request['QryGroup28'],
                "QryGroup29" => $request['QryGroup29'],
                "QryGroup30" => $request['QryGroup30'],
                "QryGroup31" => $request['QryGroup31'],
                "QryGroup32" => $request['QryGroup32'],
                "QryGroup33" => $request['QryGroup33'],
                "QryGroup34" => $request['QryGroup34'],
                "QryGroup35" => $request['QryGroup35'],
                "QryGroup36" => $request['QryGroup36'],
                "QryGroup37" => $request['QryGroup37'],
                "QryGroup38" => $request['QryGroup38'],
                "QryGroup39" => $request['QryGroup39'],
                "QryGroup40" => $request['QryGroup40'],
                "QryGroup41" => $request['QryGroup41'],
                "QryGroup42" => $request['QryGroup42'],
                "QryGroup43" => $request['QryGroup43'],
                "QryGroup44" => $request['QryGroup44'],
                "QryGroup45" => $request['QryGroup45'],
                "QryGroup46" => $request['QryGroup46'],
                "QryGroup47" => $request['QryGroup47'],
                "QryGroup48" => $request['QryGroup48'],
                "QryGroup49" => $request['QryGroup49'],
                "QryGroup50" => $request['QryGroup50'],
                "QryGroup51" => $request['QryGroup51'],
                "QryGroup52" => $request['QryGroup52'],
                "QryGroup53" => $request['QryGroup53'],
                "QryGroup54" => $request['QryGroup54'],
                "QryGroup55" => $request['QryGroup55'],
                "QryGroup56" => $request['QryGroup56'],
                "QryGroup57" => $request['QryGroup57'],
                "QryGroup58" => $request['QryGroup58'],
                "QryGroup59" => $request['QryGroup59'],
                "QryGroup60" => $request['QryGroup60'],
                "QryGroup61" => $request['QryGroup61'],
                "QryGroup62" => $request['QryGroup62'],
                "QryGroup63" => $request['QryGroup63'],
                "QryGroup64" => $request['QryGroup64'],
                "PrchseItem" => $request['PrchseItem'],
                "SellItem" => $request['SellItem'],
                "InvntItem" => $request['InvntItem'],
                "frozenFor" => $request['frozenFor'],
            ]);

            info($request['itemSalesUnitOfMeasurements']);
            ITM12::where('ItemCode', $request['ItemCode'])->delete();
            foreach ($request['itemSalesUnitOfMeasurements'] as $key => $value) {

                ITM12::create([
                    'ItemCode' => $request['ItemCode'],
                    'UomType' => $value['UoMType'],
                    'UomEntry' => $value['UoMEntry'],
                ]);
            }

            return $data;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return (new ApiResponseService())->apiIntegratorFailedResponseService($th->getMessage(), -1, "Unkown");
        }
    }

    public function searchProduct()
    {
        $ItemCode = \Request::get('code');
        $external_unique_key = \Request::get('external_unique_key');

        $data = OITM::where(function ($q) use ($ItemCode) {
            if ($ItemCode) {
                $q->where('ItemCode', $ItemCode);
            }
        })
            ->where(function ($q) use ($external_unique_key) {
                if ($external_unique_key) {
                    $q->where('ItemCode', $external_unique_key);
                }
            })->get();

        return $data;
    }

    /**
     * Creating Item Group
     */
    public function addOrUpdateItemGroup(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $value) {
            try {
                $data = OITB::updateOrcreate(
                    [
                        'ItmsGrpCod' => $value['ItmsGrpCod'],
                    ],
                    [
                        'ExtRef' => $value['ExtRef'],
                        'ItmsGrpNam' => $value['ItmsGrpNam'],
                    ]
                );
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
                return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
            }
        }

        return (new ApiResponseService())->apiSuccessResponseService("Item Group Synced");
    }
    /**
     *
     * Getting Item Categorues
     *
     */

    public function getCategories()
    {
        try {
            $data = OITB::get();
            return $data;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Search Category
     */
    public function searchCategory()
    {
        $code = \Request::get('code');
        try {
            $data = OITB::where(function ($q) use ($code) {
                if ($code) {
                    $q->where('ExtRef', $code);
                }
            })
                ->get();
            return $data;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function searchPriceList()
    {
        $ExtRef = \Request::get('ExtRef');
        $data = OPLN::where('ExtRef', $ExtRef)->get();
        return $data;
    }

    public function createPriceList(Request $request)
    {
        $priceList = OPLN::updateOrcreate([
            'ExtRef' => $request['ExtRef'],
        ], [
            'ListName' => $request['ListName'],
            'ListNum' => $request['ExtRef'],
            'ValidFor' => $request['ValidFor'],
            'Factor' => $request['Factor'],
            'BASE_NUM' => $request['BASE_NUM'],
            'PrimCurr' => 1, // Primary Currency
        ]);

        return $priceList;
    }

    public function searchUOM($extRef)
    {
        $data = OUOM::where('ExtRef', $extRef)->first();
        return $data ? $data->id : null;
    }

    public function createUOM(Request $request)
    {
        return OUOM::updateOrcreate([
            'UomCode' => $request['UomCode'],
        ], [
            'UomName' => $request['UomName'],
            'ExtRef' => $request['ExtRef'],
        ]);
    }

    public function searchUoMGroup()
    {
        $ExtRef = \Request::get('code');
        $data = OUGP::where('UgpCode', $ExtRef)->get();

        foreach ($data as $key => $value) {
            $ugp1 = UGP1::where('UgpEntry', $value->id)->get();
            $value->unitOfMeasureGroupDefinitions = $ugp1;
        }

        return $data;
    }

    public function createUomGroup(Request $request)
    {
        $header = OUGP::updateOrcreate(
            [
                'UgpCode' => $request['UgpCode'],

            ],
            [
                'ExtRef' => $request['ExtRef'],
                'UgpName' => $request['UgpName'],
                'BaseUom' => $this->searchUOM($request['BaseUom']),
                'IsManual' => $request['IsManual'],
            ]
        );

        // UGP1::where('UgpEntry', $header->id)->delete();
        foreach ($request['unitOfMeasureGroupDefinitions'] as $key => $value) {
            $data = UGP1::updateOrCreate([
                'UgpEntry' => $header->id,
                'UomEntry' => $this->searchUOM($value['UomEntry']),
            ], [
                'ExtRef' => $value['ExtRef'],
                'AltQty' => $value['AltQty'],
                'BaseQty' => $value['BaseQty'],
                'LineNum' => $value['LineNum'],
            ]);
        }

        $ugp1 = UGP1::where('UgpEntry', $header->id)->get();
        $header->unitOfMeasureGroupDefinitions = $ugp1;

        return $header;
    }

    public function searchProductPriceList()
    {
        $ItemCode = \Request::get('ItemCode');
        $PriceListCode = \Request::get('PriceListCode');

        $data = ITM1::where('ItemCode', $ItemCode)
            ->where('PriceList', $PriceListCode)
            ->get();
        return $data;
    }

    public function createProductSerialNumber(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $value) {
            $item = OITM::where('ItemCode', $value['ItemCode'])->first();
            if (!$item) {
                continue;
            }

            $itemDetails = OSRN::updateOrCreate([
                'ItemCode' => $value['ItemCode'],
                'SysNumber' => $value['SysNumber'],
            ], [
                "DistNumber" => $value['DistNumber'],
                "MnfSerial" => isset($value['MnfSerial']) ? $value['MnfSerial'] : null,
                'Colour' => isset($value['Colour']) ? $value['Colour'] : null,
                'LotNumber' => isset($value['LotNumber']) ? $value['LotNumber'] : null,
                'ItemName' => OITM::where('ItemCode', $value['ItemCode'])->value('ItemName'),
            ]);

            $serialQuantities = OSRQ::updateOrCreate([
                'ItemCode' => $value['ItemCode'],
                'SysNumber' => $value['SysNumber'],
                'WhsCode' => $value['WhsCode'],
            ], [
                "Quantity" => $value['Quantity'],
            ]);
        }
    }

    /**
     * Update Item Prices
     */

    public function updateProductPrices(Request $request)
    {

        PriceUpdateJob::dispatch($request['data']);
        return (new ApiResponseService())->apiSuccessResponseService();
    }

    /**
     * UOM Prices
     */
    public function updateProductUomPrices(Request $request)
    {
        $data = $request['data'];

        $totalSynced = 0;
        $errRes = [];
        foreach ($data as $key => $value) {
            try {
                $priceList = OPLN::where('ExtRef', $value['PriceList'])->first();

                if (!$priceList) {
                    if ($value['PriceList'] == 0) {
                        ITM9::where('ItemCode', $value['ItemCode'])->delete();
                    } else {
                        $errRes = (new ApiResponseService())
                            ->apiIntegratorFailedResponseService(
                                "Pricelist doest not exist",
                                404,
                                "Pricelist doest not exist: " . $value['PriceList']
                            );
                    }
                }

                ITM9::updateOrCreate(
                    [
                        'ItemCode' => $value['ItemCode'],
                        'PriceList' => $priceList->id,
                        'UomEntry' => OUOM::where('ExtRef', $value['UomEntry'])->value('id'),
                        'Currency' => 1,
                    ],
                    [
                        'Price' => $value['Price'],
                    ]
                );



                $totalSynced = $totalSynced + 1;
            } catch (\Throwable $th) {

                return (new ApiResponseService())
                    ->apiIntegratorFailedResponseService(
                        $th->getMessage(),
                        -1,
                        "Unkown"
                    );
            }
        }
        if (empty($errRes)) {
            return $errRes;
        }

        return (new ApiResponseService())->apiSuccessResponseService($totalSynced);
    }

    /**
     * UOM
     */
    public function getAllUoms(Request $request)
    {
        return OUOM::paginate(500);
    }
    /**
     * UOM GROUP
     */
    public function getAllUomGroup(Request $request)
    {
        return OUGP::with('ugp1.uom')->paginate(500);
    }

    /**
     * Get Enable price List
     */

    public function getEnabledPriceList()
    {
        $priceList = [
            "ManualSync" => false,
            "pricelists" => [2]
        ];
        return $priceList;
    }
}
