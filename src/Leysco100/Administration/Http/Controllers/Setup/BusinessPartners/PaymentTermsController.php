<?php

namespace App\Http\Controllers\API\Administration\Setup\BusinessPartners;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\BusinessPartner\Models\PaymentTerm;
use App\Domains\Shared\Services\ApiResponseService;

class PaymentTermsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = PaymentTerm::get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
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
        $this->validate($request, [
            'PymntGroup' => 'required|unique:payment_terms',
        ]);
        $details = [
            'PymntGroup' => $request['PymntGroup'],
            'GroupNum' => $request['GroupNum'],
            'BslineDate' => $request['BslineDate'],
            'PayDuMonth' => $request['PayDuMonth'],
            'ExtraMonth' => $request['ExtraMonth'],
            'ExtraDays' => $request['ExtraDays'],
            'OpenRcpt' => $request['OpenRcpt'],
            'DiscCode' => $request['DiscCode'],
            'VolumDscnt' => $request['VolumDscnt'],
            'LatePyChrg' => $request['LatePyChrg'],
            'CredLimit' => $request['CredLimit'],
            'ObligLimit' => $request['ObligLimit'],
            'ListNum' => $request['ListNum'],
        ];
        $newPaymentterm = new PaymentTerm(array_filter($details));
        $newPaymentterm->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = PaymentTerm::with('ocrd')
            ->where('id', $id)
            ->first();
        if (!$data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Payment Term  Does not exist");
        }
        try {
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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
        //
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
}
