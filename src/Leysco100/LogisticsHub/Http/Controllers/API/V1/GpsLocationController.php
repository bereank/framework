<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;

use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\LogisticsHub\Models\GpsLocation;

class GpsLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $date = $request->input('date') ?? false;

        $latestRecords = DB::connection("tenant")->table('gps_locations')
            ->select(DB::connection("tenant")->raw('MAX(id) as id'))
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('created_at', $date);
            })
            ->groupBy('user_id')
            ->get();

        $gpsLocations = DB::connection("tenant")->table('gps_locations')
            ->whereIn('id', $latestRecords->pluck('id'))
            ->get();

        return (new ApiResponseService())->apiSuccessResponseService($gpsLocations);
    }

    public function store(Request $request)
    {


        $rules = [
           // 'user_id' => 'required|unique:tenant.users,id',
           'user_id' => 'required',
            'email' => 'required|email',
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiFailedResponseService($validator->errors()->first());
        }
        $gpsLocation = GpsLocation::create($request->all());
        return (new ApiResponseService())->apiSuccessResponseService($gpsLocation);
    }
    public function show(Request $request, $user_id)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $full_day = $request->input('full_day');

        $gpsLocation = GpsLocation::where('user_id', $user_id)
            ->whereDate('created_at', $date);
        if ($full_day) {
            $gpsLocation = $gpsLocation->get();
        } else {
            $gpsLocation =  $gpsLocation->latest('created_at')
                ->first();
        }


        return (new ApiResponseService())->apiSuccessResponseService($gpsLocation);
    }
}
