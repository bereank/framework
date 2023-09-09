<?php

namespace Leysco100\Gpm\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\GMS1;



class GPMDashboardController  extends Controller
{
    public function index()
    {
        try {
            $param = Request::get('periods');
            $hasPeriods = \Request::has('periods');
            $hasEnd = \Request::has('endDate');
            $hasStart = \Request::has('startDate');
            $periods = explode(',', $param);

            $report = [];
            if (!(!$hasPeriods && ($hasStart || $hasEnd))) {
                foreach ($periods as $period) {
                    $data =   $this->getDateRange($period);
                    $startDate = $data[0];
                    $endDate = $data[1];
                    $periodName = $data[2];
                    $summary = $this->ReportSummary($startDate, $endDate, $periodName);
                    array_push($report,  $summary);
                }
            }
            $StartDate =  \Request::get('startDate');
            $EndDate =  \Request::get('endDate');

            if ($hasEnd && $hasStart) {
                $name = 'customRange';
                $CustomSummary = $this->ReportSummary($StartDate, $EndDate, $name);

                array_push($report,  $CustomSummary);
            }
            return (new ApiResponseService())->apiSuccessResponseService($report);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    function getDateRange($interval)
    {

        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($interval) {
            case 'daily':
                $startDate = Carbon::now()->startOfDay();
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::createFromTimestamp(0)->setTimezone('UTC');
                $interval = 'all';
                break;
        }

        return [$startDate, $endDate, $interval];
    }

    public function ReportSummary($startDate, $endDate, $name)
    {
        $user = Auth::user();

        $totalScans = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalScans->where('UserSign', $user->id);
        }
        $totalScans = number_format($totalScans->count());

        $totalFaildDoesNotExistY = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalFaildDoesNotExistY->where('UserSign', $user->id);
        }
        $totalFaildDoesNotExistY = number_format($totalFaildDoesNotExistY->where('Status', 1)->count());


        $totalFailedDuplicateY = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalFailedDuplicateY->where('UserSign', $user->id);
        }
        $totalFailedDuplicateY = number_format($totalFailedDuplicateY->where('Status', 2)->count());


        $totalSuccessfulReleased = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalSuccessfulReleased->where('UserSign', $user->id);
        }
        $totalSuccessfulReleased = $totalSuccessfulReleased->where('Status', 0)->where('Released', 1)->count();


        $totalSuccessfulNotReleased = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalSuccessfulNotReleased->where('UserSign', $user->id);
        }
        $totalSuccessfulNotReleased = $totalSuccessfulNotReleased->where('Status', 0)->where('Released', 0)->count();

        $totalFlagged = GMS1::whereBetween('created_at', [$startDate, $endDate]);
        if (!$user->SUPERUSER) {
            $totalFlagged->where('UserSign', $user->id);
        }
        $totalFlagged =  number_format($totalFlagged->where('Status', 3)->count());


        $summary = [
            'Period' => $name,
            'startDate' =>  $startDate,
            'endDate' => $endDate,
            'totalScans' => $totalScans,
            'totalFaildDoesNotExist' => $totalFaildDoesNotExistY,
            'totalFailedDuplicate' => $totalFailedDuplicateY,
            'totalSuccessfulReleased' => $totalSuccessfulReleased,
            'totalSuccessfulNotReleased' => $totalSuccessfulNotReleased,
            'totalFlagged' => $totalFlagged
        ];
        return $summary;
    }
}