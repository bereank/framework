<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Financials;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OCRPP;
use Leysco100\Administration\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $data = OCRPP::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
        $this->validate($request, [
            'CreditCard' => 'required',
            'AcctCode' => 'required',
            'Phone' => 'required|numeric',
        ]);
        try {
            $data = OCRPP::create([
                'CreditCard' => $request['CreditCard'] ?? null,
                'AcctCode' => $request['AcctCode'] ?? null,
                'Phone' => $request['Phone'] ?? null,
                'UserSign' => Auth::user()->id,
                'IntTaxCode' => $request['IntTaxCode'] ?? null,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
        $this->validate($request, [
            'CreditCard' => 'required',
            'AcctCode' => 'required',
            'Phone' => 'required|numeric',
        ]);
        try {
            $data = OCRPP::where('id', $id)->update([
                'CreditCard' => $request['CreditCard'] ?? null,
                'AcctCode' => $request['AcctCode'] ?? null,
                'Phone' => $request['Phone'] ?? null,
                'UserSign2' => Auth::user()->id,
                'IntTaxCode' => $request['IntTaxCode'] ?? null,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
}
