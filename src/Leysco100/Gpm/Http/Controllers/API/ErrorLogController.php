<?php

namespace Leysco100\Gpm\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Leysco100\Shared\Models\MobileErrorLog;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Gpm\Services\NotificationsService;
use Leysco100\Shared\Services\ApiResponseService;


class ErrorLogController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $UserSign =  request()->filled('UserSign') ? request()->input('UserSign') : false;
        $startDate = request()->filled('StartDate') ? Carbon::parse(request()->input('StartDate'))->startOfDay() : Carbon::now()->startOfDay();
        $endDate = request()->filled('EndDate') ? Carbon::parse(request()->input('EndDate'))->endOfDay() : Carbon::now()->endOfDay();
        $ErrorCode =  request()->filled('ErrorCode') ? request()->input('ErrorCode') : false;
        $Version =  request()->filled('Version') ? request()->input('Version') : false;
        $HttpMethod =  request()->filled('HttpMethod') ? request()->input('HttpMethod') : false;
        $Make =  request()->filled('Make') ? request()->input('Make') : false;
        try {
            $logs = MobileErrorLog::when(!empty($UserSign), function ($query) use ($UserSign) {
                return $query->where('UserSign', $UserSign);
            })
                ->when(!empty($ErrorCode), function ($query) use ($ErrorCode) {
                    return $query->where('ErrorCode', $ErrorCode);
                })
                ->when(!empty($Version), function ($query) use ($Version) {
                    return $query->where('Version ', $Version);
                })
                ->when(!empty($HttpMethod), function ($query) use ($HttpMethod) {
                    return $query->where('HttpMethod', $HttpMethod);
                })
                ->when(!empty($Make), function ($query) use ($Make) {
                    return $query->where('Make', $Make);
                })
                ->with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($logs);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $data = $request->all();
        //ErrorLog::create($data);
        $recipient = ["john.muchira@leysco.co.ke"];
        $ErrorCode = $request['ErrorCode'] ?? null;
        $Message = $request['Message'] ?? null;
        $AdditionalData = $request['AdditionalData'] ?? null;
        $AppType =  $request['AppType'] ?? null;
        $BaseURL =  $request['BaseURL'] ?? null;
        $Version = $request['Version'] ?? null;

        $message = 'Message=>' . $Message . PHP_EOL .
            'ErrorCode=>' . $ErrorCode . PHP_EOL .
            'AdditionalData=>' . $AdditionalData . PHP_EOL .
            'BaseURL=>' . $BaseURL . PHP_EOL .
            'AppType=>' . $AppType . PHP_EOL .
            'Version=>' . $Version;
        $subject = 'Mobile Error Logs';

        Mail::raw($message, function ($mail) use ($recipient, $subject) {
            $mail->to($recipient)->subject($subject);
        });
    }


    public function show($id)
    {
        try {
            $log = MobileErrorLog::with('user')->findOrFail($id);
            return (new ApiResponseService())->apiSuccessResponseService($log);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function edit($id)
    {
        $log = MobileErrorLog::findOrFail($id);
    }

    public function update(Request $request, $id)
    {

        $log = MobileErrorLog::findOrFail($id);
        $data = $request->all();
        $log->update($data);
    }

    public function destroy($id)
    {
        $log = MobileErrorLog::findOrFail($id);
        $log->delete();
    }
}
