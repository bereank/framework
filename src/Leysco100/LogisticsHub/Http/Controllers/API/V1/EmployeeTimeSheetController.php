<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;


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
    public function index()
    {
        try {
            $data = ETS1::with('user:id,name')->get();
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
                'ClockIn' => 'required|date_format:H:i',
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
            $Yattendance = ETS1::where('UserSign', $user_id)->where('date', '>', $ClockInDate)
            ->orwhere('ClockOut', '=', '00:00:00')
            ->orwhere('ClockOut', null)->first();

            if ($Yattendance) {
                return (new ApiResponseService())
                    ->apiFailedResponseService([
                        'message' => 'Clock out first.',
                        'data' =>  $Yattendance
                    ]);
            }
            $attendance = ETS1::where('UserSign', $user_id)->where('date', '=', $ClockInDate)
            ->orwhere('ClockOut', '=', '00:00:00')
            ->orwhere('ClockOut', null)->get()->toArray();

            if ($attendance) {
                return (new ApiResponseService())->apiFailedResponseService('Employee Attendance Already Created.');
            } else {
                $date = date("Y-m-d");

                $totalLateSeconds = strtotime($request['ClockIn']) - strtotime($date . $startTime);

                $hours = floor($totalLateSeconds / 3600);
                $mins  = floor($totalLateSeconds / 60 % 60);
                $secs  = floor($totalLateSeconds % 60);
                $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

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
                $timesheet->Late          = $late;
                // $timesheet->EarlyLeaving = $earlyLeaving;
                // $timesheet->OverTime      = $overtime;
                $timesheet->TotalRest    = '00:00:00';
                $timesheet->UserSign    =    $user->id;

                $timesheet->save();
            }

            return (new ApiResponseService())->apiSuccessResponseService("Created Successfullty");
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
            $ETS1 = ETS1::findOrFail($id);

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

                'Comment' => 'nullable',
                'Date' => 'nullable|date',
                'ClockOut' => 'required|date_format:H:i'

            ]);

            $user_id = Auth::user()->id;
            $user = User::with('oudg')->where('id', $user_id)->first();
            if (!$user->oudg && !$user->oudg->EtstCode) {
                return (new ApiResponseService())->apiFailedResponseService('Employee Timesheet Not Set.');
            }
            $code = $user->oudg->EtstCode;

            $sheet = ETST::where('id', $code)->first();
            $ClockInDate = $request['Date'] ?? date("Y-m-d");

            $endTime    =  $sheet->CheckOutTime;

            $date = date("Y-m-d");

            //early Leaving
            $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($request['ClockOut']);
            $hours                    = floor($totalEarlyLeavingSeconds / 3600);
            $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
            $secs                     = floor($totalEarlyLeavingSeconds % 60);
            $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);


            if (strtotime($request['ClockOut']) > strtotime($date . $endTime)) {
                //Overtime
                $totalOvertimeSeconds = strtotime($request['ClockOut']) - strtotime($date . $endTime);
                $hours                = floor($totalOvertimeSeconds / 3600);
                $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                $secs                 = floor($totalOvertimeSeconds % 60);
                $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            $timesheet->update([
                'DocEntry' => $sheet->id,
                'Date'          => $request['Date'] ?? now(),
                'Status'        => 'Present',

                'ClockOut'     => $request['ClockOut'],
                'Comment'     => $request['Comment'],

                'EarlyLeaving' => $earlyLeaving,
                'OverTime' => $overtime,
                'TotalRest'    => '00:00:00',
                'UserSign'    =>    $user->id,

            ]);


            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfullty");
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
}
