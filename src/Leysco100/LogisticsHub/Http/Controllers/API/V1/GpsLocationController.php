<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\OGPS;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\OUDG;
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
        try {
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
                ->get()->map(function ($gpsLocation) {
                    return [
                        'lat' => (float) number_format($gpsLocation->latitude, 6),
                        'lng' => (float)number_format($gpsLocation->longitude, 6),
                        "id" => $gpsLocation->id,
                        "user_id" => $gpsLocation->user_id,
                        "email" => $gpsLocation->email,
                        "name" => $gpsLocation->name,
                        "latitude" => $gpsLocation->latitude,
                        "longitude" => $gpsLocation->longitude,
                        "address" => $gpsLocation->address,
                        "created_at" => $gpsLocation->created_at,


                    ];
                });


            $latitude = DB::connection("tenant")->table('gps_locations')
                ->whereIn('id', $latestRecords->pluck('id'))->whereNotNull('latitude')->avg('latitude');
            $longitude = DB::connection("tenant")->table('gps_locations')
                ->whereIn('id', $latestRecords->pluck('id'))->whereNotNull('longitude')->avg('longitude');


            $centerMap = [
                'lat' => (float)number_format($latitude, 6) ?? 1.9099,
                'lng' => (float)number_format($longitude, 6) ?? 34.9099,
            ];

            $gpsL = collect([
                'centermap' => $centerMap,
                'locations' => $gpsLocations,
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($gpsL);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $user_id = Auth::user();
            $user_dflt = OUDG::where('id', $user_id->DfltsGroup)->first();


            if (!$user_dflt->GpsActive) {
                return (new ApiResponseService())->apiFailedResponseService("Gps Location Tracking Not Activated");
            }
            $set_up = OGPS::find($user_dflt->GpsSetUpID);
            $current_time = Carbon::now();
            if (!$current_time->between(
                Carbon::createFromFormat('H:i:s', $set_up->start_time),
                Carbon::createFromFormat('H:i:s', $set_up->end_time)
            ) && $set_up->Active) {
                return (new ApiResponseService())->apiFailedResponseService("Not allowed to track Gps Location at this time");
            }

            $rules = [
                //  'user_id' => 'required|unique:tenant.users,id',
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
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show(Request $request, $user_id)
    {

        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));

            $full_day = $request->input('full_day') == 'true' ? true : false;

            $gpsLocation = GpsLocation::where('user_id', $user_id)
                ->whereDate('created_at', $date);

            if ($full_day) {

                $gpsLocation = $gpsLocation->get()->map(function ($gpsLocation) {
                    return [
                        'lat' => (float) $gpsLocation->latitude,
                        'lng' => (float) $gpsLocation->longitude,
                        "id" => $gpsLocation->id,
                        "user_id" => $gpsLocation->user_id,
                        "email" => $gpsLocation->email,
                        "name" => $gpsLocation->name,
                        "latitude" => $gpsLocation->latitude,
                        "longitude" => $gpsLocation->longitude,
                        "address" => $gpsLocation->address,
                        "created_at" => $gpsLocation->created_at,


                    ];
                });
            } else {
                $gpsLocation = $gpsLocation->latest('created_at')->limit(1)->get()
                    ->map(function ($gpsLocation) {
                        return [
                            'lat' => (float) $gpsLocation->latitude,
                            'lng' => (float) $gpsLocation->longitude,
                            "id" => $gpsLocation->id,
                            "user_id" => $gpsLocation->user_id,
                            "email" => $gpsLocation->email,
                            "name" => $gpsLocation->name,
                            "latitude" => $gpsLocation->latitude,
                            "longitude" => $gpsLocation->longitude,
                            "address" => $gpsLocation->address,
                            "created_at" => $gpsLocation->created_at,
                        ];
                    });;
            }


            $latitude = GpsLocation::where('user_id', $user_id)->whereNotNull('latitude')->avg('latitude');
            $longitude = GpsLocation::where('user_id', $user_id)->whereNotNull('longitude')->avg('longitude');


            $centerMap = [
                'lat' => (float) $latitude ?? 1.9099,
                'lng' => (float) $longitude ?? 34.9099,
            ];


            $gpsL = collect([
                'centermap' => $centerMap,
                'locations' => $gpsLocation,
            ]);

            return (new ApiResponseService())->apiSuccessResponseService($gpsL);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
