<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRG;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;



class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updated_at_gteq = \Request::get('updated_at_gteq');

        try {
            $data = OCRD::with('territory', 'tiers', 'channels')
                ->where(function ($q) use ($updated_at_gteq) {
                    if ($updated_at_gteq) {
                        $q->whereDate('updated_at', $updated_at_gteq);
                    }
                })
                ->get();
            return $data;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $created = OCRD::create([
            'CardCode' => $request['CardCode'],
            'CardName' => $request['CardName'],
            'CardFName' => $request['CardFName'],
            'Phone1' => $request['Phone1'], // Tel
            'Phone2' => $request['Phone2'], // Tel 2
            'GroupNum' => $request['GroupNum'],
            'CntctPrsn' => $request['CntctPrsn'],
            'Territory' => $request['region_id'],
            'ExtRef' => $request['ExtRef'],
            'ListNum' => $request['ListNum'],
            'frozenFor' => $request['frozenFor'],
            'CardType' => $request['CardType'] == "cCustomer" ? "C" : "S",
            'GroupCode' => $request['GroupCode'],
            'Currency' => $request['Currency'],
            'Address' => $request['Address'],
            'CreditLine' => $request['CreditLine'],
            'Balance' => $request['Balance'],
            'LicTradNum' => $request['LicTradNum'],
            'UserSign' => Auth::user()->id,
            'SlpCode' => $request['SlpCode'] != -1 ? $request['SlpCode'] : null,
        ]);

        return $created;
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
            $data = OCRD::with('territory', 'tier', 'channels')->where('id', $id)->first();
            return $data;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $customer = OCRD::where('id', $id)->first();
        if ($customer) {
            $customer->update([
                'CardName' => $request['CardName'],
                'CardFName' => $request['CardFName'],
                'Phone1' => $request['Phone1'], // Tel
                'Phone2' => $request['Phone2'], // Tel 2
                'Territory' => $request['region_id'],
                'GroupNum' => $request['GroupNum'],
                'CntctPrsn' => $request['CntctPrsn'],
                'ExtRef' => $request['ExtRef'],
                'ListNum' => $request['ListNum'],
                'frozenFor' => $request['frozenFor'],
                'GroupCode' => $request['GroupCode'],
                'Currency' => $request['Currency'],
                'isBlocked' => $request['isBlocked'],
                'Address' => $request['Address'],
                'CreditLine' => $request['CreditLine'],
                'Balance' => $request['Balance'],
                'LicTradNum' => $request['LicTradNum'],
                'SlpCode' => $request['SlpCode'] != -1 ? $request['SlpCode'] : null,
            ]);

            return $customer;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function searchRetailer()
    {
        $CardCode = \Request::get('CardCode');
        $external_unique_key = \Request::get('external_unique_key');
        try {
            $data = OCRD::with('region')
                // ->select('id', 'CardName', 'CardCode',
                //     'GroupCode', 'Phone1', 'Phone2', 'ListNum', 'frozenFor', 'ExtRef', 'TierCode'
                // )

                ->where(function ($q) use ($CardCode) {
                    if ($CardCode) {
                        $q->where('CardCode', $CardCode);
                    }
                })
                ->where(function ($q) use ($external_unique_key) {
                    if ($external_unique_key) {
                        $q->where('ExtRef', $external_unique_key);
                    }
                })
                ->get();

            foreach ($data as $key => $value) {
                $value->tier = null;
            }

            return $data;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function searchCustomerGroup()
    {
        $GroupCode = \Request::get('GroupCode');
        try {
            $data = OCRG::where('ExtRef', $GroupCode)->get();
            return $data;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function createCustomerGroup(Request $request)
    {
        $GroupCode = \Request::get('GroupCode');
        try {
            return OCRG::updateOrcreate([
                'GroupCode' => $request['GroupCode'],
                'GroupName' => $request['GroupName'],
                'GroupType' => "C",
                'ExtRef' => $request['ExtRef'],
            ]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
