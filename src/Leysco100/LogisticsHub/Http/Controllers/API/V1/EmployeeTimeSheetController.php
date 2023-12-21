<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\ETS1;
use Leysco100\Shared\Models\Administration\Models\ETST;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Administration\Http\Controllers\Controller;

class EmployeeTimeSheetController extends Controller
{
    /**
     * Display a listing of the ETS1s.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);


            $data = ETS1::with('user:id,name');
            if ($request->has('search')) {
                $search = $request->input('search');
                $data = $data->where(function ($query) use ($search) {
                    $query->orWhere('ClockOut', 'LIKE', "%{$search}%")
                        ->orWhereDate('created_at', 'LIKE', "%{$search}%")
                        ->orWhere('ClockIn', 'LIKE', "%{$search}%")
                        ->orWhere('date', 'LIKE', "%{$search}%");
                });
            }
            $data = $data->latest()
                ->paginate($perPage, ['*'], 'page', $page);


            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Store a newly created ETS1 in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([

                'Comment' => 'nullable',
                'Date' => 'nullable|date',
                'ClockIn' => 'required|date_format:H:i:s',
                // 'ClockOut' => 'required|date_format:H:i'

            ]);

            $user_id = Auth::user()->id;
            $user = User::with('oudg')->where('id', $user_id)->first();
            if (!$user->oudg && !$user->oudg->EtstCode) {
                return (new ApiResponseService())->apiFailedResponseService('Employee Timesheet Not Set.');
            }
            $code = $user->oudg->EtstCode;

            $sheet = ETST::where('id', $code)->first();
            $ClockInDate = $request['Date'] ?? date("Y-m-d");
            $startTime  =  $sheet->CheckInTime;
            $endTime    =  $sheet->CheckOutTime;
            $Yattendance = ETS1::where('UserSign', $user_id)->whereDate('date', '<', $ClockInDate)
                ->where('ClockOut', null)
                ->where('ClockIn', '!=', null)
                ->first();

            if ($Yattendance) {
                return (new ApiResponseService())
                    ->apiSuccessResponseService([
                        'type' => 1,
                        'message' => 'Please clock out for the previous day first.',
                        'data' =>  $Yattendance
                    ]);
            }

            $attendance = ETS1::where('UserSign', $user_id)->whereDate('date', '=', $ClockInDate)
                ->where('ClockIn', '!=', null)
                ->where('ClockOut', null)
                ->first();

            if ($attendance) {
                return (new ApiResponseService())->apiSuccessResponseService([
                    'type' => 2,
                    'message' => 'Employee Attendance Already Created.',
                    'data' =>  $attendance
                ]);
            } else {
                $date = date("Y-m-d");

                $totalLateSeconds = strtotime($request['ClockIn']) - strtotime($date . $startTime);
                $late = '00:00:00';
                if ($totalLateSeconds > 0) {
                    $hours = floor($totalLateSeconds / 3600);
                    $mins  = floor($totalLateSeconds / 60 % 60);
                    $secs  = floor($totalLateSeconds % 60);
                    $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }
                // //early Leaving
                // $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($request['ClockOut']);
                // $hours                    = floor($totalEarlyLeavingSeconds / 3600);
                // $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
                // $secs                     = floor($totalEarlyLeavingSeconds % 60);
                // $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);


                // if (strtotime($request['ClockOut']) > strtotime($date . $endTime)) {
                //     //Overtime
                //     $totalOvertimeSeconds = strtotime($request['ClockOut']) - strtotime($date . $endTime);
                //     $hours                = floor($totalOvertimeSeconds / 3600);
                //     $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                //     $secs                 = floor($totalOvertimeSeconds % 60);
                //     $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                // } else {
                //     $overtime = '00:00:00';
                // }

                $timesheet                = new ETS1();
                $timesheet->DocEntry = $sheet->id;
                $timesheet->Date          = $request['Date'] ?? now();
                $timesheet->Status        = 'Present';
                $timesheet->ClockIn      = $request['ClockIn'];
                $timesheet->ClockOut     = $request['ClockOut'];
                $timesheet->Comment     = $request['Comment'];
                $timesheet->Late          = $late ?? null;
                // $timesheet->EarlyLeaving = $earlyLeaving;
                // $timesheet->OverTime      = $overtime;
                $timesheet->TotalRest    = '00:00:00';
                $timesheet->UserSign    =    $user->id;

                $timesheet->save();
            }

            return (new ApiResponseService())->apiSuccessResponseService([
                'message' => "Created Successfully",
                'data' => $timesheet
            ]);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified ETS1.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $ETS1 = ETS1::with('setup')->findOrFail($id);

            return (new ApiResponseService())->apiSuccessResponseService($ETS1);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Update the specified ETS1 in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $timesheet = ETS1::findOrFail($id);


            $validatedData = $request->validate([
                'Comment' => 'nullable'
            ]);
            $ClockOutTime = Carbon::now()->format('Y-m-d H:i');


            $user_id = Auth::user()->id;
            $user = User::with('oudg')->where('id', $user_id)->first();
            if (!$user->oudg && !$user->oudg->EtstCode) {
                return (new ApiResponseService())->apiFailedResponseService('Employee Timesheet Not Set.');
            }
            $code = $user->oudg->EtstCode;

            $sheet = ETST::where('id', $code)->first();

            $endTime    =  $sheet->CheckOutTime;

            $date = date("Y-m-d");

            //early Leaving
            $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($ClockOutTime);
            $hours                    = floor($totalEarlyLeavingSeconds / 3600);
            $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
            $secs                     = floor($totalEarlyLeavingSeconds % 60);
            $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);


            if (strtotime($ClockOutTime) > strtotime($date . $endTime)) {
                //Overtime
                $totalOvertimeSeconds =  strtotime($ClockOutTime) - strtotime($date . $endTime);
                $hours                = floor($totalOvertimeSeconds / 3600);
                $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                $secs                 = floor($totalOvertimeSeconds % 60);
                $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            $timesheet->update([
                'DocEntry' => $sheet->id,
                // 'Date'          => $request['Date'] ?? now(),
                // 'Status'        => 'Present',

                'ClockOut'     => Carbon::now()->format('H:i:s'),
                'Comment'     => $request['Comment'],

                'EarlyLeaving' => $earlyLeaving,
                'OverTime' => $overtime,
                'TotalRest'    => '00:00:00',
                'UserSign'    =>    $user->id,

            ]);


            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified ETS1 from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ETS1 = ETS1::findOrFail($id);

            $ETS1->delete();

            return (new ApiResponseService())->apiSuccessResponseService("Deleted Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getClocInDetails()
    {
        try {
            $id = Auth::user()->id;
            $ETS1 = ETS1::where('UserSign', $id)
                ->where(function ($query) {
                    $query->orwhere('ClockOut', '=', '00:00:00')
                        ->orwhere('ClockOut', null);
                })
                ->first();

            return (new ApiResponseService())->apiSuccessResponseService($ETS1);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function confirmClockIn(Request $request)
    {
        try {
            $ClockInDate = $request['date'] ?? date("Y-m-d");
            $ClockInDate = Carbon::parse($ClockInDate);
            $user_id = Auth::user()->id;
            $Yattendance = ETS1::where('UserSign', $user_id)->whereDate('date', '<', $ClockInDate)
                ->where('ClockOut', null)
                ->where('ClockIn', '!=', null)
                ->first();

            if ($Yattendance) {
                return (new ApiResponseService())
                    ->apiSuccessResponseService([
                        'type' => 1,
                        'message' => 'Please clock out for the previous day first.',
                        'data' =>  $Yattendance
                    ]);
            }

            $attendance = ETS1::where('UserSign', $user_id)->whereDate('date', '=', $ClockInDate)
                ->where('ClockIn', '!=', null)
                ->where('ClockOut', null)
                ->first();

            if ($attendance) {
                return (new ApiResponseService())->apiSuccessResponseService([
                    'type' => 2,
                    'message' => 'Employee Attendance Already Created.',
                    'data' =>  $attendance
                ]);
            }

            if (!$Yattendance && !$attendance) {
                return (new ApiResponseService())->apiSuccessResponseService([
                    'type' => 3,
                    'message' => 'Create attendance'
                ]);
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
