<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;



class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesEmp = \Request::has('SlpCode') ? explode(",", \Request::get('SlpCode')) : [];
        $RlpCode = \Request::has('RlpCode') ? explode(",", \Request::get('RlpCode')) : [];
        $Status =  request()->filled('Status') ? request()->input('Status') : false;
        $UserSign =  request()->filled('UserSign') ?  explode(",", \Request::get('UserSign')) : [];
        $CardCode =  request()->filled('CardCode') ?  explode(",", \Request::get('CardCode')) : [];
        try {
            $currentTime = Carbon::now();

            $user_data = Auth::user();

            $data = OCLG::with('outlet:id,CardCode,CardName,Address,Phone1')
                ->with('objectives', 'employees')

                // ->whereDate('CallDate', '>=', date('Y-m-d'))
                // ->where(function ($query) use ($currentTime) {
                //     $query->where(function ($subQuery) use ($currentTime) {
                //         $subQuery->whereDate('CallDate', '=', $currentTime->toDateString())
                //             ->whereTime('CallTime', '<=', $currentTime->toTimeString());
                //     })->orWhere(function ($subQuery) use ($currentTime) {
                //         $subQuery->whereDate('CloseDate', '=', $currentTime->toDateString())
                //             ->whereTime('CloseTime', '>=', $currentTime->toTimeString());
                //     });
                // })


                ->when($Status, function ($query) use ($Status) {
                    return $query->where('Status', $Status);
                })
                ->when($salesEmp, function ($query) use ($salesEmp) {
                    return $query->whereIn('SlpCode', $salesEmp);
                })
                ->when($RlpCode, function ($query) use ($RlpCode) {
                    return $query->whereIn('RlpCode', $RlpCode);
                })
                ->when($CardCode, function ($query) use ($CardCode) {
                    return $query->whereIn('CardCode', $CardCode);
                })
                ->when($UserSign, function ($query) use ($UserSign) {
                    return $query->whereIn('UserSign', $UserSign);
                })
                // ->where(function ($query) use ($user_data) {
                //     $query->orwhere('UserSign', $user_data->id)
                //         ->orwhere('RlpCode', $user_data?->oudg?->Driver);
                // })
                ->latest()
                ->get();
            foreach ($data as $value) {
                $value->CallCode = rand(1, 300);
            }
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
        $validator = Validator::make($request->all(), [
            'CardCode' => 'required',
            'CallDate' => 'required|date|after_or_equal:today',
            'CallTime' => 'required',
        ]);
        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }
        $otherCalls = OCLG::whereDate('CallDate', $request['CallDate'])
            ->where('CardCode', $request['CardCode'])
            ->get();

        if (sizeof($otherCalls)) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("Another Call is already schedule for that date");
        }

        try {
            $user = Auth::user();
            $OCLG = OCLG::create([
                'ClgCode' => $request['ClgCode'],
                'SlpCode' => $request['SlpCode'] ?? OUDG::where('id', $user->DfltsGroup)->value('SalePerson'), // Sales Employee
                'CardCode' => $request['CardCode'], // Oulet/Customer
                'CallDate' => $request['CallDate'], //  Call Date
                'CallTime' => $request['CallTime'], // CallTime
                'UserSign' => $user->id,
                'RouteCode' => $request['RouteCode'] ?? null,
                'CallEndTime' => $request['CallEndTime'] ?? null, // CallTime
                'CloseDate' => $request['CloseDate'] ?? null,
                'CloseTime' => $request['CloseTime'] ?? null,
                'Repeat' => $request['Repeat'] ? $request['Repeat'] : "N", // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
                'Summary' => $request['Summary'] ?? null,
                'Status' => $request['Status'] ?? 'D',
            ]);

            return (new ApiResponseService())->apiSuccessResponseService(['data' => $OCLG, "message" => "Call Created Successfully"]);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
        try {
            $OCLG = OCLG::with('outlet:id,CardCode,CardName,Address,Phone1')
                ->with('objectives', 'employees')
                ->where('id', $id)
                ->first();
            return (new ApiResponseService())->apiSuccessResponseService($OCLG);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
        $OCLG = OCLG::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'CardCode' => 'required',
            'CallDate' => 'required|date|after_or_equal:today',
            'CallTime' => 'required',
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }
        try {
            $OCLG->update(array_filter($request->all()));
            return (new ApiResponseService())->apiSuccessResponseService(['data' => $OCLG, "message" => "Call Updated Successfully"]);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }


    public function callActions(Request $request, $id)
    {

        try {
            if ($request["type"] == "O") {

                $call = OCLG::where('id', $id)->update([
                    "OpenedDate" => now(),
                    "Status" => "O",
                    "OpenedTime" => now(),
                ]);

                return (new ApiResponseService())->apiSuccessResponseService(['data' => $call, "message" => "Call Opened Successfully"]);
            }
            if ($request["type"] == "C") { {
                    $call = OCLG::where('id', $id)
                        ->where('Status', "O")
                        ->update([
                            "CloseDate" => now(),
                            "CloseTime" => now(),
                            "Status" => "C",
                        ]);

                    return (new ApiResponseService())->apiSuccessResponseService(['data' => $call, "message" => "Call closed Successfull"]);
                }
            }
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
