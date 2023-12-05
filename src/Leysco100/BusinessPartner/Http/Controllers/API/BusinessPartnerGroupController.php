<?php


namespace Leysco100\BusinessPartner\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRG;
use Leysco100\BusinessPartner\Http\Controllers\Controller;

class BusinessPartnerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $bpgrp = OCRG::get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($bpgrp);
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
        try {

            $bpgrp  = OCRG::create([
                'GroupCode' => $request['GroupCode'],
                'Locked' => $request['Locked'],
                'GroupName' => $request['GroupName'],
                'GroupType' => $request['GroupType'],
                'UserSign' => Auth::user()->id,
                'PriceList' => $request['PriceList'],
                'DiscRel' => $request['DiscRel'],
                'EffecPrice' => $request['EffecPrice'],
                'ExtRef' => $request['ExtRef'],
            ]);
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
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
        try {
            $bpgrp = OCRG::where('id', $id)->first();
            return (new ApiResponseService())
                ->apiSuccessResponseService($bpgrp);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $bpgrp = OCRG::where('id', $id)->update([
                'GroupCode' => $request['GroupCode'],
                'Locked' => $request['Locked'],
                'GroupName' => $request['GroupName'],
                'GroupType' => $request['GroupType'],
                'UserSign' => Auth::user()->id,
                'PriceList' => $request['PriceList'],
                'DiscRel' => $request['DiscRel'],
                'EffecPrice' => $request['EffecPrice'],
                'ExtRef' => $request['ExtRef'],
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($bpgrp);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
