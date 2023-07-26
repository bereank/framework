<?php
namespace Leysco100\Administration\Http\Controllers\Setup\Financials;





use Illuminate\Http\Request;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Currency::get();
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
            'currency' => 'required',
             'CurrCode' => 'required',
        ]);
        return Currency::create([
            'currency' => $request['currency'],
             'CurrCode' => $request['CurrCode'],
        ]);
    }


}
