<?php

namespace App\Http\Controllers\API\Administration\Setup\Financials;

use App\Domains\Administration\Models\TaxGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaxGroup::get();
    }
    public function TaxGroupType($type)
    {
        if ($type == 'input') {
            return TaxGroup::where('category', 'I')->get();
        } else {
            return TaxGroup::where('category', 'O')->get();
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
