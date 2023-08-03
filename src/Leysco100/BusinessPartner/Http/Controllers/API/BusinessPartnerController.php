<?php

namespace App\Http\Controllers\API\BusinessPartner;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\BusinessPartner\Models\CRD15;
use Leysco100\BusinessPartner\Http\Controllers\Controller;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // (new AuthorizationService())->checkIfAuthorize(1, 'read');
        try {
            $data = OCRD::with('territory', 'tiers', 'channels', 'octg')
                ->take(100)
                ->orderBy('id', 'desc')
                ->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function getVendors()
    {
        $data = OCRD::where('CardType', "S")->get();

        $counter = 1;
        foreach ($data as $key => $value) {
            $value->counter = $counter++;
        }
        return $data;
    }

    public function getCustomers()
    {
        $search = \Request::get('f');

//        $data = OCRD::with('octg')->where('CardType', "C")
//            ->where(function ($q) use ($search) {
//                if ($search != null) {
//                    $q->where('CardCode', 'LIKE', "%$search%")
//                        ->orWhere('CardName', 'LIKE', "%$search%")
//                        ->orWhere('CardFName', 'LIKE', $search);
//                }
//            })
//            ->take(10)
//            ->get();

        $data = OCRD::where('CardType', "C")
            ->where(function ($q) use ($search) {
                if ($search != null) {
                    $q->where('CardCode', 'LIKE', "%$search%")
                        ->orWhere('CardName', 'LIKE', "%$search%")
                        ->orWhere('CardFName', 'LIKE', $search);
                }
            })
            ->select(['id','CardCode','CardName','CardFName','LicTradNum','Currency'])
            ->take(10)
            ->get();
        $counter = 1;
        foreach ($data as $key => $value) {
            $value->counter = $counter++;
        }
        return $data;
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
            $nnm1 = NNM1::where('id', $request['Series'])->first();
            if ($nnm1->IsManual == "Y") {
                $CardCode = $request['CardCode'];
            } else {
                $CardCode = $nnm1->BeginStr . sprintf("%0" . $nnm1->NumSize . "s", $nnm1->NextNumber) . $nnm1->EndStr;
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
        }

        DB::beginTransaction();
        try {
            $bpCreate = OCRD::create([
                'CardCode' => $CardCode,
                'CardName' => $request['CardName'],
                'CardFName' => $request['CardFName'],
                'GroupCode' => $request['GroupCode'],
                'Currency' => $request['Currency'],
                'VatGroup' => $request['VatGroup'],
                'CmpPrivate' => $request['CmpPrivate'],

                //General
                'CntctPrsn' => $request['CntctPrsn'],
                'Territory' => $request['Territory'],
                'SlpCode' => $request['SlpCode'],
                'Phone1' => $request['Phone1'], // Tel
                'Phone2' => $request['Phone2'], // Tel 2
                'Cellular' => $request['Cellular'], // Cellular
                'E_Mail' => $request['E_Mail'], // E_Mail

                //Payment Terms
                'ListNum' => $request['ListNum'],
                'CreditLine' => $request['CreditLine'],

                //Accounting
                'DebPayAcct' => $request['DebPayAcct'], //Account Receivalbe
                'DpmClear' => $request['DpmClear'], //Clearing Account
                'DpmIntAct' => $request['DpmIntAct'], //    DPM Interim Account

                //Distribution
                'ChannCode' => $request['ChannCode'], //Channel Code
                'TierCode' => $request['TierCode'], //Tier Code
                'Distributor' => $request['Distributor'], //Distributor

                //Location
                'Longitude' => $request['Longitude'], //Longitude
                'Latitude' => $request['Latitude'], //Latitude

                //Numberinging Series
                'Series' => $request['Series'],
            ]);

            foreach ($request['crd15'] as $key => $value) {
                $itemPrice = CRD15::create([
                    'CardCode' => $bpCreate->id,
                    'GroupCode' => $value['id'], // id Propery e.g Colour ID
                    'QryGroup' => array_key_exists('QryGroup', $value) ? $value['QryGroup'] : null, // id for property desc, e.g white
                ]);
            }

            NumberingSeries::dispatch($request['Series']);
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::rollback();
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
        $data = OCRD::with(
            'territory',
            'tiers',
            'channels',
            'contacts',
            'call',
            'orders.rdr1',
        )->where('id', $id)->first();

        if (!$data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Item Does not exist");
        }

        try {
            $crd15 = CRD15::where('CardCode', $data->id)->get();
            foreach ($crd15 as $key => $value) {
                $value->GroupName = OCQG::where('id', $value->GroupCode)->value('GroupName');
                $value->cqg1 = CQG1::where('GroupCode', $value->GroupCode)->get();
            }
            $data->crd15 = $crd15;
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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
            $data = OCRD::where('id', $id)->update([
                // 'CardCode' => $request['CardCode'],
                'CardName' => $request['CardName'],
                'CardFName' => $request['CardFName'],
                'GroupCode' => $request['GroupCode'],
                'Currency' => $request['Currency'],
                'VatGroup' => $request['VatGroup'],

                //General
                'CntctPrsn' => $request['CntctPrsn'],
                'Territory' => $request['Territory'],
                'SlpCode' => $request['SlpCode'],

                //Payment Terms
                'ListNum' => $request['ListNum'],
                'CreditLine' => $request['CreditLine'],

                //Accounting
                'DebPayAcct' => $request['DebPayAcct'], //p
                'DpmClear' => $request['DpmClear'], //Clearingf Account
                'DpmIntAct' => $request['DpmIntAct'], //    DPM Interim Account

                //Distribution
                'ChannCode' => $request['ChannCode'], //Channel Code
                'TierCode' => $request['TierCode'], //Tier Code
                'Distributor' => $request['Distributor'], //Distributor

                //Location
                'Longitude' => $request['Longitude'], //Longitude
                'Latitude' => $request['Latitude'], //Latitude

            ]);

            $Items = $request['crd15'];
            $IsItems = is_array($Items) ? 'Yes' : 'No';
            if ($IsItems == "No") {
                foreach ($request['crd15'] as $key => $value) {
                    $itemPrice = CRD15::where('id', $value->id)->update([
                        'GroupCode' => $value['id'], // id Propery e.g Colour ID
                        'QryGroup' => $value['QryGroup'], // id for property desc, e.g white
                    ]);
                }
            }
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
        }
    }



    //Made
    public function getCustomerDocuments($CustomerID, $ObjType)
    {
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        return $DocumentTables->ObjectHeaderTable::with(
            'outlet',
            'objecttype',
            'document_lines.oitm.itm1',
            'document_lines.oitm.inventoryuom',
            'document_lines.oitm.ougp.ouom',
            'document_lines.oitm.oitb'
        )
            ->where('CardCode', $CustomerID)
            ->get();
    }

    public function getCustomerDocumentsStatus($CustomerID, $ObjType, $DocStatus)
    {
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        return $DocumentTables->ObjectHeaderTable::with(
            'outlet',
            'objecttype',
            'document_lines.oitm.itm1',
            'document_lines.oitm.inventoryuom',
            'document_lines.oitm.ougp.ouom',
            'document_lines.oitm.oitb'
        )
            ->where('CardCode', $CustomerID)
            ->where('DocStatus', $DocStatus)
            ->get();
    }

    public function getDistributors()
    {
        return OCRD::where('Distributor', 'Y')->with('outlet', 'employees')->get();
    }

    // public function importBusinessPartner(Request $request)
    // {
    //     $property1 = OCQG::updateOrCreate([
    //         'GroupName' => "Area",
    //     ]);

    //     $property2 = OCQG::updateOrCreate([
    //         'GroupName' => "Cluster",
    //     ]);

    //     $data = OCRG::updateOrCreate([
    //         'GroupName' => "GT",
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $array = Excel::toCollection(new BPImport(), request()->file('bpData'));
    //         foreach ($array as $key => $value) {
    //             $dataImports = $value;
    //             foreach ($dataImports as $key => $val) {
    //                 $newData = OCRD::updateOrCreate([
    //                     'CardName' => $val[0],
    //                 ], [
    //                     'CardCode' => (new SystemDefaults())->getDftNumberingSeries(1),
    //                 ]);
    //             }
    //         }
    //         DB::commit();
    //         return (new ApiResponseService())->apiSuccessResponseService();
    //     } catch (\Throwable $th) {
    //         DB::rollback();
    //         return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
    //     }
    // }
}
