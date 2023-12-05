<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;


use Illuminate\Http\Request;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\Administration\Models\SLP1;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Administration\Http\Controllers\Controller;

class TerritoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OTER::whereNull('parent')
                ->with('childrenRecursive')
                ->get();

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
            'descript' => 'required',
        ]);
        return OTER::create([
            'descript' => $request['descript'],
            'parent' => $request['parent'],
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
        $territory = OTER::findOrfail($id);

        $subregions = OTER::where('parent', $id)->get();
        foreach ($subregions as $key => $value) {
            $value->salesReps = SLP1::where('Territory', $value->id)->count();
            $value->subRegions = OTER::where('parent', $value->id)->count();
            $value->totalOutlets = OCRD::where('Territory', $value->Territory)->count();
        }
        $territory->outlets = OCRD::where('Territory', $id)->get();
        $latitude = OCRD::where('Territory', $id)->avg('Latitude');
        $longitude = OCRD::where('Territory', $id)->avg('Longitude');
        $territory->centerMap = [
            'lat' => $latitude,
            'lng' => $longitude,
        ];
        $salesEmployees = OSLP::select('id', 'SlpName')->get();
        foreach ($salesEmployees as $key => $value) {
            $value->AssignedToRegion = SLP1::where('Territory', $id)->where('SlpCode', $value->id)->count() > 0 ? 1 : 0;
        }
        $territory->salesReps = $salesEmployees;
        $territory->subregions = $subregions;
        return $territory;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = OTER::findOrFail($id);
        $data->delete();
    }

    public function getOutlets(Request $request)
    {
        return OCRD::WhereIn('Territory', $request['regions'])->get();
    }
}
