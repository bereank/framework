<?php
namespace Leysco100\Administration\Http\Controllers\Setup\General;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\Administration\Models\SLP1;
use Leysco100\Administration\Http\Controllers\Controller;


class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ORLP::with('channel', 'tier', 'vehicle')->get();
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
            'RlpName' => 'required',
            'RlpCode' => 'required'
        ]);
        $employee = ORLP::create([
            'RlpName' => $request['RlpName'], //name
            'RlpCode' => $request['RlpCode'], //Code
            'ChannCode' => $request['ChannCode'], // channel
            'TierCode' => $request['TierCode'], // tier
            'Locked' => $request['Locked'],
            'Active' => $request['Active'], //N=Inactive, Y=Active
            'Telephone' => $request['Telephone'], // Telephone
            'Mobil' => $request['Mobil'], //Mobile
            'Email' => $request['Email'], //Email
            'vehicle_id' => $request['vehicle_id'], //vehicle
        ]);

        $regions = $request['Regions'];
        $IsRegions = is_array($regions) ? 'Yes' : 'No';
        // if ($IsRegions == "Yes") {
        //     foreach ($regions as $key => $value) {
        //         $RegionDetails = [
        //             'ElpCode' => $employee->id,
        //             'Territory' => $value,
        //         ];
        //         $newRegion = new RSP1($RegionDetails);
        //         $newRegion->save();
        //     }
        // }

        return (new ApiResponseService())->apiSuccessResponseService($employee);
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
        $driver = ORLP::with('channel', 'tier')
            ->where('id', $id)
            ->first();

        return $driver;
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
            $this->validate($request, [
                'RlpName' => 'required',
                'RlpCode' => 'required',
            ]);
            $data = ORLP::where('id', $id)->update([
                'RlpName' => $request['RlpName'], //name
                'RlpCode' => $request['RlpCode'], //Code
                'ChannCode' => $request['ChannCode'], // channel
                'TierCode' => $request['TierCode'], // tier
                'Locked' => $request['Locked'],
                'Active' => $request['Active'], //N=Inactive, Y=Active
                'Telephone' => $request['Telephone'], // Telephone
                'Mobil' => $request['Mobil'], //Mobile
                'Email' => $request['Email'], //Email
                'vehicle_id' => $request['vehicle_id'], //vehicle
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

   
    public function getForRegion($TerritoryID)
    {
        $section = OTER::where('id', $TerritoryID)
            ->first();
        $section->ids = $section->getAllChildren()->pluck('id');
        $regions = $section->ids;
        $employee = ORLP::with('regions')->whereHas('regions', function ($q) use ($regions, $TerritoryID) {
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
}
