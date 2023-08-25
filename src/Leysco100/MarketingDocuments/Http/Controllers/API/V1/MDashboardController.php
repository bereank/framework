<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\TargetSetup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;



class MDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $calls = OCLG::whereDate('created_at', Carbon::today())
                ->latest()
                ->count();

            $TotalOutlets = OCRD::where('CardType', 'C')->where('frozenFor', "N")->count();

            $startnow = now()->startOfWeek()->format('Y-m-d');
            $endnow = now()->endOfWeek()->format('Y-m-d');
            $period = CarbonPeriod::create($startnow, $endnow);

            $CurrentWeekCallsSummary = [];
            foreach ($period as $key => $value) {
                $TotalCalls = OCLG::whereDate('CallDate', $value)->count();
                $details = [
                    "CallDate" => $value->format('Y-m-d'),
                    "TotalCalls " => $TotalCalls,
                ];

                array_push($CurrentWeekCallsSummary, $details);
            }
            $startMONTH = now()->startOfWeek()->format('Y-m-d');
            $endMONTH = now()->endOfWeek()->format('Y-m-d');
            $user_id = Auth::user()->id;
            $results = OINV::select(DB::raw('SUM(VatSum) as VatSum'), DB::raw('SUM(DocTotal) as TotalDocTotal'))
                ->where("UserSign", $user_id)
                ->where('CANCELED', "=", 'N')
                ->whereBetween('DocDate', [$startMONTH, $endMONTH])->get();
            $results = $results->first();
            $TotalMonthlySales =  ($results->TotalDocTotal  - $results->VatSum);

            $userSummaryData = $this->salesSummaryReports();
            $data = [
                'CallsToday' => $calls,
                'SalesWeekPerc' =>  0,
                'SalestargetMonthly' => 0,
                'SalesTargetDaily' => 0,
                'TotalOutlets' => $TotalOutlets,
                'TotalOrders' => $userSummaryData['totalOrders'],
                'AllOrdersTotalValue' => $userSummaryData['totalOrdersValue'],
                'TotalOrdersToday' => $userSummaryData['totalOrdersToday'],
                'TodayOrdersTotalValue' => $userSummaryData['totalOrdersValueToday'],
                'TotalMonthlySales' =>     $TotalMonthlySales ?? 0,
                'CurrentWeekCallsSummary' => $CurrentWeekCallsSummary,
            ];
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * User Summary Data
     */
    public function salesSummaryReports()
    {
        $user_id = Auth::user()->id;
        $oslp = OSLP::findOrFail($user_id);

        $totalOrders = ORDR::where(function ($q) {
            $user = Auth::user();
            if ($user->id != 8 || $user->id != 1) {
                $q->where('UserSign', $user->id);
            }
        })->count();

        $totalOrdersValue = ORDR::where(function ($q) {
            $user = Auth::user();
            // $oslp = OSLP::findOrFail($user->id);
            if ($user->id != 8 || $user->id != 1) {
                $q->where('UserSign', $user->id);
            }
        })->sum('ExtDocTotal');

        $totalOrdersToday = ORDR::where(function ($q) {
            $user = Auth::user();
            if ($user->id != 8 || $user->id != 1) {
                $q->where('UserSign', $user->id);
            }
        })->whereDate('created_at', Carbon::today())->count();

        $totalOrdersValueToday = ORDR::where(function ($q) {
            $user = Auth::user();
            if ($user->id != 8 || $user->id != 1) {
                $q->where('UserSign', $user->id);
            }
        })->whereDate('created_at', Carbon::today())
            ->sum('ExtDocTotal');



        // $now = Carbon::now();

        // $targets = TargetSetup::with('document_lines')->whereMonth('TtoDate',  $now->month)->where('SlpCode', $oslp->SlpCode)->where('RecurPat', 'M')->get();
        // $TotalMonthlyTargets = $targets->sum(function ($target) {
        //     return $target->document_lines->sum('Tvalue');
        // });


        // $totalDays = date('t');


        // $TotalDailyTargets = round($TotalMonthlyTargets / $totalDays);

        // if ($TotalMonthlyTargets != 0) {
        //     $SalesWeekPerc = round(($totalOrdersValue / $TotalMonthlyTargets) * $totalDays);
        // } else {
        //     $SalesWeekPerc = 0;
        // }

        $details = [
            'totalOrders' => $totalOrders,
            'totalOrdersValue' => $totalOrdersValue,
            'totalOrdersToday' => $totalOrdersToday,
            'totalOrdersValueToday' => $totalOrdersValueToday,
            // 'TotalMonthlyTargets' => $TotalMonthlyTargets,
            // 'TotalDailyTargets' => $TotalDailyTargets,
            // 'SalesWeekPerc' => $SalesWeekPerc,
        ];

        return $details;
    }
}
