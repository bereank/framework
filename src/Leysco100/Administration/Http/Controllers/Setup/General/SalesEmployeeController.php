<?php

namespace App\Http\Controllers\API\Administration\Setup\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domains\Marketing\Models\ORDR;
use App\Domains\Administration\Models\OADM;
use App\Domains\Administration\Models\OSLP;
use App\Domains\Administration\Models\OTER;
use App\Domains\Administration\Models\SLP1;
use App\Domains\BusinessPartner\Models\OCLG;
use App\Domains\BusinessPartner\Models\OCRD;
use App\Domains\Shared\Services\ApiResponseService;

class SalesEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OSLP::with('channel', 'tier')->get();
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
        try {
            $employee = OSLP::create([
                'SlpName' => $request['SlpName'], //employee's name
                'ChannCode' => $request['ChannCode'], // employee's channel
                'TierCode' => $request['TierCode'], // employee's tier
                'Locked' => $request['Locked'],
                'Active' => $request['Active'], //N=Inactive, Y=Active
                'Telephone' => $request['Telephone'], // Telephone
                'Mobil' => $request['Mobil'], //Mobile
                'Email' => $request['Email'], //Email
                'SlpCode' => $request['SlpCode'],
                // 'job_title' => $request['job_title'],
            ]);

            if (isset($request['Regions'])) {
                foreach ($request['Regions'] as $key => $value) {
                    $RegionDetails = [
                        // 'SlpCode' => $employee->id,
                        'SlpCode' => $employee->SlpCode,
                        'Territory' => $value,
                    ];
                    $newRegion = new SLP1($RegionDetails);
                    $newRegion->save();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function addNewRegion(Request $request)
    {
        $this->validate($request, [
            'SlpCode' => 'required|numeric',
        ]);

        foreach ($request['Regions'] as $key => $value) {
            $RegionDetails = [
                'SlpCode' => $request['SlpCode'],
                'Territory' => $value,
            ];
            $newRegion = new SLP1($RegionDetails);
            $newRegion->save();
        }
        return response()->json(
            [
                'message' => "Region Added Successfully",
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();

        //Getting Single Employee Records
        $salesEmployee = OSLP::with('channel', 'tier')->where('id', $id)->first();
        //Getting Calls Created sales Emplooe
        $Calls = OCLG::with('outlet')->where('SlpCode', $salesEmployee->id)->get();
        $Orders = ORDR::where('SlpCode', $salesEmployee->id)->get();
        $assignedRegions = SLP1::with('regions')->where('SlpCode', $id)->get();
        foreach ($assignedRegions as $key => $value) {
            $value->salesReps = SLP1::where('Territory', $value->id)->count();
            $value->subRegions = OTER::where('parent', $value->id)->count();
            $value->totalOutlets = OCRD::where('Territory', $value->Territory)->count();
        }
        $salesEmployee->Calls = $Calls;
        $salesEmployee->Orders = $Orders;
        $salesEmployee->assignedRegions = $assignedRegions;
        return $salesEmployee;
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
        try {
            $request->validate([
                'SlpName' => 'required|string|max:255',
                'SlpCode' => 'required|max:50',
            ]);
            $employee = OSLP::find($id);

            $employee->update([
                'SlpName' => $request['SlpName'], //employee's name
                'SlpCode' => $request['SlpCode'], // employee's code
            ]);

            $employee->save();

            return (new ApiResponseService())->apiSuccessResponseService();
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

    public function getForRegion($TerritoryID)
    {
        $section = OTER::where('id', $TerritoryID)
            ->first();
        $section->ids = $section->getAllChildren()->pluck('id');
        $regions = $section->ids;
        $employee = OSLP::with('regions')->whereHas('regions', function ($q) use ($regions, $TerritoryID) {
            $q->whereIn('Territory', $regions)->orWhere('Territory', $TerritoryID);
        })->get();
        return $employee;
    }

    public function removeFromRegion($TerritoryID, $EmployeeID)
    {
        $SLP1 = SLP1::where('SlpCode', $EmployeeID)->where('Territory', $TerritoryID)->first();
        if ($SLP1) {
            $SLP1->delete();
            return response()->json(
                [
                    'message' => "Deleted Successfully",
                ],
                201
            );
        }
    }

    public function addEmployeeToRegion($TerritoryID, $EmployeeID)
    {
        $SLP1 = SLP1::updateOrCreate([
            'SlpCode' => $EmployeeID,
            'Territory' => $TerritoryID,
        ]);
        return response()->json(
            [
                'message' => "Added Successfully",

            ],
            201
        );
    }

    public function setDefault($EmployeeID)
    {
        try {
            $data = [
                'DfltSlp' => $EmployeeID, // Default Sales Employee id
            ];
            OADM::where('id', 1)->update($data);
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
