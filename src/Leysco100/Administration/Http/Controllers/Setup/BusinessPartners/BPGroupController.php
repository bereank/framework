<?php

namespace Leysco100\Administration\Http\Controllers\Setup\BusinessPartners;

use Illuminate\Http\Request;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRG;
use Leysco100\Administration\Http\Controllers\Controller;



class BPGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OCRG::get();
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
        if (!$request['GroupName']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Group Name Required");
        }

        try {
            $data = OCRG::create(
                [
                    'GroupCode' => count(OCRG::get()) + 100,
                    'GroupName' => $request['GroupName'],
                    'GroupType' => $request['GroupType'] ? $request['GroupType'] : "C",
                    'PriceList' => $request['PriceList'],
                    'DiscRel' => $request['DiscRel'] ? $request['DiscRel'] : "L",
                    'EffecPrice' => $request['EffecPrice'] ? $request['EffecPrice'] : "D",
                ]
            );
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
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
    public function show($type)
    {
        try {
            $data = OCRG::with('opln')->where('GroupType', $type)->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
