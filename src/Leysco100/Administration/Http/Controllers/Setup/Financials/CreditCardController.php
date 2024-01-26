<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Financials;

use Illuminate\Http\Request;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\Banking\Models\OCRC;
use Leysco100\Shared\Services\ApiResponseService;

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $data = OCRC::with('paymentMetod')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            'name' => 'required',
            'code' => 'required',
            'rate' => 'required|numeric',
        ]);

        return TaxGroup::create([
            'name' => $request['name'],
            'code' => $request['code'],
            'inactive' => $request['inactive'],
            'category' => $request['category'],
            'group_desc' => $request['group_desc'],
            'rate' => $request['rate'],
            'effectivedate' => $request['effectivedate'],
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
            'name' => $request['name'],
            'code' => $request['code'],
            'inactive' => $request['inactive'],
            'category' => $request['category'],
            'group_desc' => $request['group_desc'],
            'rate' => $request['rate'],
            'account' => 1,
            'effectivedate' => $request['effectivedate'],
        ];
        TaxGroup::where('id', $id)->update($details);
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
