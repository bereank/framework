<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Illuminate\Http\Request;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Finance\Models\OCCT;
use Leysco100\Shared\Models\Finance\Models\OPRC;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Jobs\PriceUpdateJob;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\SalesOportunities\Models\OOCR;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;


class ISharedController extends Controller
{
    public function storeErrorLog(Request $request)
    {
        $data = EOTS::firstOrcreate([
            'DocEntry' => $request['DocEntry'],
            'ObjType' => $request['ObjType'],
        ], [
            'ErrorMessage' => $request['errorText'],
        ]);

        return $data;
    }

    /**
     *
     */

    public function createOrUpdareCostCenterType(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $value) {
            OCCT::updateOrCreate([
                'CctCode' => $value['CctCode'],
            ], [
                'CctName' => $value['CctName'],
            ]);
        }
    }

    /**
     *
     */

    public function createOrUpdareCostCenters(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $value) {

            /**
             * Const Centrer Codes
             */
            $costcentertype = OCCT::where('CctCode', $value['CCTypeCode'])->first();

            /**
             * Cost Centes
             */

            OPRC::updateOrCreate([
                'PrcCode' => $value['PrcCode'],
            ], [
                'PrcName' => $value['PrcName'],
                'DimCode' => $value['DimCode'],
                'Active' => $value['Active'],
                'CCTypeCode' => $costcentertype ? $costcentertype->id : null,
            ]);

            $generalCostCenters = ['Centr_z', 'Centr_z2', 'Centr_z3', 'Centr_z4', 'Centr_z5'];
            if (!in_array($value['PrcCode'], $generalCostCenters)) {
                OOCR::updateOrCreate([
                    'OcrCode' => $value['PrcCode'],
                ], [
                    'OcrName' => $value['PrcName'],
                    'OcrTotal' => 100,
                    'Active' => $value['Active'],
                    'DimCode' => $value['DimCode'],
                ]);
            }
        }
    }

    /**
     *
     */

    public function createOrUpdateDistributionRules(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $value) {
            $newDist = OOCR::updateOrCreate([
                'OcrCode' => $value['OcrCode'],
            ], [
                'OcrName' => $value['OcrName'],
                'OcrTotal' => $value['OcrTotal'],
                'Active' => $value['Active'],
                'Direct' => $value['Direct'],
                'DimCode' => $value['DimCode'],
            ]);
        }
    }

    /**
     *
     */

    public function getObjectUpdateStatus(Request $request)
    {
        return APDI::get();
    }

    /**
     * Update Item Prices
     */

    public function updateProductPrices(Request $request)
    {
        $data = $request['data'];

        $totalSynced = 0;
        PriceUpdateJob::dispatch($data);
        return (new ApiResponseService())->apiSuccessResponseService($totalSynced);
    }
}
