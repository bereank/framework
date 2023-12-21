<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OIBQ;
use Leysco100\Shared\Models\LogisticsHub\Models\RouteAssignment;
use Leysco100\Shared\Models\LogisticsHub\Models\RoutePlanning;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\ONNM;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\Shared\Models\BusinessPartner\Models\OCPR;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRG;
use Leysco100\Shared\Models\MarketingDocuments\Models\INV1;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODPI;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Leysco100\Shared\Models\MarketingDocuments\Models\RDR1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;


class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $date = date("Y-m-d");

        $user = User::where('id', Auth::user()->id)->with('oudg')->first();

        $RouteActive =   $user->oudg?->RouteActive ?? false;

        $outletIds = [];

        if ($RouteActive) {
            $assignments = RouteAssignment::where("Date",$date)->with("route.outlets")->where("SlpCode",$user->oudg->SalePerson)->get();
            foreach ($assignments as $assignment){
                $outletIds =  $assignment->route->outlets->pluck("id");
            }
        }

        $data = OCRD::select('id', 'CardCode', 'CardName', 'Address', 'PriceListNum', 'Longitude', 'Latitude')
            ->with('contacts')
            ->where(function ($q) use ($outletIds){
                if (count($outletIds)>0){
                    $q->whereIn("id",$outletIds);
                }
            })
            ->orderBy('CardName', 'asc')
            ->where('CardName', '!=', null)
            ->where('CardType', 'C')
            ->where('frozenFor', "N")
            ->get();

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
        $this->validate($request, [
            'CardName' => 'required|unique:tenant.o_c_r_d_s',
        ]);
        $totalOutlet = OCRD::count();
        $CardCode = "M" . $totalOutlet;

        //Get APDI Details
        $ObjectID = APDI::where('ObjectID', 1)->value('id');
        $onmm = ONNM::where('id', $ObjectID)->first();

        $nnm1 = NNM1::where('id', $onmm->DfltSeries)->first();
        $CardCode = $nnm1->BeginStr . sprintf("%0" . $nnm1->NumSize . "s", $nnm1->NextNumber) . $nnm1->EndStr;
        $user = Auth::user();
        $Created = OCRD::create([
            'CardCode' => $CardCode,
            'CardName' => $request['CardName'],
            'Longtitude' => $request['Longtitude'],
            'Latitude' => $request['Latitude'],
            'Address' => $request['Address'],
            'UserSign' => $user->id,
        ]);

//        NumberingSeries::dispatchSync($onmm->DfltSeries);
        return response()
            ->json(
                [
                    'message' => "Created Successfully",
                ],
                201
            );
    }

    public function CreateContactPerson(Request $request)
    {
        $this->validate($request, [
            'Name' => 'required',
            'CardCode' => 'required|integer',
        ]);
        $user = Auth::user();
        return OCPR::create([
            'CardCode' => $request['CardCode'],
            'Name' => $request['Name'],
            'Position' => $request['Position'],
            'Address' => $request['Address'],
            'Tel1' => $request['Tel1'],
            'UserSign' => $user->id,
        ]);
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
            $data = OCRD::with('territory', 'tiers', 'channels', 'contacts', 'call', 'orders.rdr1', 'crd15.ocqg', 'crd15.cqg1')->where('id', $id)->first();

            $data->TotalBalance = OINV::where('CardCode', $id)
                ->where('DocStatus', 0)
                ->sum('DocTotal') + ODPI::where('CardCode', $id)->sum('DocTotal');

            $data->AllCalls = OCLG::with('employees')->where('CardCode', $id)->get();
            $data->TotalCalls = OCLG::where('CardCode', $id)
                ->count();
            $data->PendingCalls = OCLG::where('CardCode', $id)
                ->where('Status', 'N')
                ->count();
            $data->ClosedCalls = OCLG::where('CardCode', $id)
                ->where('Status', 'Y')
                ->count();
        } catch (\Throwable $th) {
            Log::info($th);
            throw $th;
        }

        // foreach ($data as $key => $value) {

        //     $value->AllInvoices = OINV::where('CardCode', $value->id)
        //         ->select('id', 'CardCode', 'DocType', 'DocTotal', 'UserSign', 'Comments', 'created_at')
        //         ->where('DocStatus', 0)
        //         ->get();
        //     $orders = ORDR::select('id', 'CardCode', 'DocType', 'DocTotal', 'DocStatus', 'UserSign', 'created_at')
        //         ->where('DocStatus', 0)
        //         ->get();
        //     foreach ($orders as $key => $order) {
        //         $Items = RDR1::
        //             select('id', 'Quantity', 'Price', 'LineTotal', 'ItemCode')
        //             ->with('ItemDetails:id,ItemCode,ItemName')
        //             ->where('LineStatus', "O")
        //             ->where('DocEntry', $order->id)
        //             ->get();
        //         $order->OrderedItems = $Items;
        //     }
        //     $value->OpenOrders = $orders;

        // }
        return $data;
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
        $customer = OCRD::findOrFail($id);
        $customer->update([
            'Longitude' => $request['Longitude'],
            'Latitude' => $request['Latitude'],
            'Address' => $request['Address'],
            'Phone1'=>$request['Phone1'] ?? null
        ]);

        return $customer;
    }

    public function SingleOutlet($id, $type)
    {
        try {
            $OutletID = $id;
            $Type = $type;
            if ($Type == 'invoices') {
                $data = OINV::with('outlet')
                    ->latest()
                    ->where('DocStatus', "O")
                    ->where('CardCode', $OutletID)
                    ->get();

                foreach ($data as $key => $value) {
                    $Items = INV1::select('id', 'Quantity', 'Price', 'LineTotal', 'ItemCode')
                        ->with('ItemDetails:id,ItemCode,ItemName')
                        ->where('DocEntry', $value->id)
                        ->get();
                    $value->OrderedItems = $Items;
                }
                return $data;
            } elseif ($Type == 'orders') {
                $data = ORDR::with('outlet')
                    ->with('CreatedBy:id,name')
                    ->latest()
                    ->where('DocStatus', "O")
                    ->where('CardCode', $OutletID)
                    ->get();

                foreach ($data as $key => $value) {
                    $Items = RDR1::select('id', 'Quantity', 'Price', 'LineTotal', 'ItemCode')
                        ->with('ItemDetails:id,ItemCode,ItemName')
                        ->where('DocEntry', $value->id)
                        ->get();
                    $value->OrderedItems = $Items;
                }
                return $data;
            }
        } catch (\Throwable $th) {
            Log::info($th);
            throw $th;
        }
    }

    public function customerMapFilter(Request $request)
    {
        return OCRD::WhereIn('Territory', $request['Territory'])
            ->orwhereBetween('created_at', [$request['from'], $request['to']])
            ->orWhereIn('SlpCode', $request['employees'])
            ->get();
    }

    public function outletCategory()
    {
        try {
            $data = OCRG::where('GroupType', "C")->get();
            return $data;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
