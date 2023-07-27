<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Financials;

use Illuminate\Http\Request;



use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;

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


}
