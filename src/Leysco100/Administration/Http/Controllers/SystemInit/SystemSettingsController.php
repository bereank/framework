<?php

namespace App\Http\Controllers\API\Administration\Setup\SystemInit;

use App\Http\Controllers\Controller;
use App\OADM;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OADM::with('opln')->where('id', 1)->first();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $details = [
            'CostPrcLst' => $request['CostPrcLst'],  //Based Price Origin
            'DfSVatItem' => $request['DfSVatItem'], //Sales Tax Groups Items
            'DfSVatServ' => $request['DfSVatServ'], //Sales Tax Groups Service
            'DfPVatItem' => $request['DfPVatItem'],  //Purchase Tax Groups Items
            'DfPVatServ' => $request['DfPVatServ'], //Purchaes Tax Groups Items
        ];
        OADM::where('id', 1)->update(array_filter($details));
        return "Updated";
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
