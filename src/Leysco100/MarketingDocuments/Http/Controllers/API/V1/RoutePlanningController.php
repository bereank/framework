<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\Shared\Models\RouteOutlet;
use Leysco100\Shared\Models\RoutePlanning;

class RoutePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RoutePlanning::with('calls.outlet', 'bpartners','territory')->get();
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
        $user = Auth::user();

        $this->validate($request, [
            'name' => 'required', //description
        ]);

        return RoutePlanning::create([
            'name' => $request['name'],
            'user_id' => $user->id,
            'territory_id' => $request["region"]["id"],
            'description' => $request['description'] ? $request['description'] : "Desc",
        ]);
    }

    public function createRouteOutlets(Request $request)
    {

        //Creating Rows:
        foreach ($request['bpartners'] as $key => $item) {
            $HeaderItems = RouteOutlet::updateOrcreate([
                'route_id' => $request['route_id'], //User
                'outlet_id' => $item, //Outlet Val
            ]);
        }
    }

    public function createRouteCalls(Request $request)
    {
        $user = Auth::user();
        //Creating Calls:
        foreach ($request['outlets'] as $key => $value) {
            $OCLG = OCLG::firstOrCreate([
                'RouteCode' => $request['RouteCode'], // Sales Employee
                'SlpCode' => $request['SlpCode'], // Sales Employee
                'CardCode' => $value['id'], // Oulet/Customer
                'CallDate' => $request['CallDate'], //  Call Date
            ], [
                'CallTime' => $value['StartTime'], // CallTime
                'CallEndTime' => $value['EndTime'], // CallTime
                'Repeat' => $request['Repeat'], // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
                'UserSign' => $user->id,
            ]);
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
        return RoutePlanning::with('bpartners.territory', 'calls.outlet', 'calls.employees')->findOrfail($id);
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
        $user = Auth::user();

        $this->validate($request, [
            'name' => 'required',
        ]);

        $route = [
            'name' => $request['name'],
            'user_id' => $user->id,
            'description' => $request['description'],
        ];
        RoutePlanning::where('id', $id)->update($route);
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
