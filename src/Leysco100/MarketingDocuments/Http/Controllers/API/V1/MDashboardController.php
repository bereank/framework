<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\User;
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
            $user = Auth::user();
            $user = User::with('oudg')->where('id', $user->id)->First();
            $SlpCode =  request()->filled('SlpCode') ?  request('SlpCode') : false;

            $calls = OCLG::whereDate('created_at', Carbon::today())
                ->latest()
                ->count();

            $TotalOutlets = OCRD::where('CardType', 'C')->where('frozenFor', "N")->count();

            $startnow = now()->startOfWeek()->format('Y-m-d');
            $endnow = now()->endOfWeek()->format('Y-m-d');
            $period = CarbonPeriod::create($startnow, $endnow);

            $CurrentWeekCallsSummary = [];
            foreach ($period as $key => $value) {
                $TotalCalls = OCLG::whereDate('CallDate', $value)
                    ->when(!$user->SUPERUSER, function ($query) use ($user) {
                        $query->where("UserSign", $user->id);
                    })
                    ->when($SlpCode, function ($query) use ($SlpCode) {
                        $query->where("SlpCode",  $SlpCode);
                    })
                    ->count();
                $details = [
                    "CallDate" => $value->format('Y-m-d'),
                    "TotalCalls " => $TotalCalls,
                ];

                array_push($CurrentWeekCallsSummary, $details);
            }
            $startMONTH = now()->startOfMonth()->format('Y-m-d');
            $endMONTH = now()->endOfMonth()->format('Y-m-d');

            $results = OINV::select(DB::raw('SUM(VatSum) as VatSum'), DB::raw('SUM(DocTotal) as TotalDocTotal'))
                ->when(!$user->SUPERUSER, function ($query) use ($user) {
                    $query->where("UserSign", $user->id);
                })
                ->when($SlpCode, function ($query) use ($SlpCode) {
                    $query->where("SlpCode",  $SlpCode);
                })
                ->where('CANCELED', "=", 'N')
                ->whereBetween('DocDate', [$startMONTH, $endMONTH])->get();
            $results = $results->first();
            $TotalMonthlySales =  ($results->TotalDocTotal  - $results->VatSum);

            $userSummaryData = $this->salesSummaryReports($startMONTH,  $endMONTH, $SlpCode, $user);
            $data = [
                'CallsToday' => $calls,
                'SalesWeekPerc' =>  0,
                'SalestargetMonthly' => 0,
                'SalesTargetDaily' => 0,
                'TotalOutlets' => $TotalOutlets,
                'SummaryData' => $userSummaryData,
                'TotalOrders' => 0,
                'AllOrdersTotalValue' => 0,
                'TotalOrdersToday' => 0,
                'TodayOrdersTotalValue' => 0,
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
    public function salesSummaryReports($startMONTH, $endMONTH, $SlpCode, $user)
    {

        $objects = [13, 17, 15, 14];

        $docSummary = [];

        $tables = APDI::with('pdi1')
            ->whereIn('ObjectID', $objects)
            ->get();

        foreach ($tables as $table) {
            $startWeek = now()->startOfWeek()->format('Y-m-d');
            $endWeek = now()->endOfWeek()->format('Y-m-d');

            $ordersQuery = $table->ObjectHeaderTable::when(!$user->SUPERUSER, function ($query) use ($user) {
                $query->where("UserSign", $user->id);
            })
                ->when($SlpCode, function ($query) use ($SlpCode) {
                    $query->where("SlpCode",  $SlpCode);
                });

            $total = $ordersQuery->count();
            $totalValue = $ordersQuery->sum('DocTotal');
            $totalToday = $ordersQuery->whereDate('created_at', Carbon::today())->count();
            $totalValueToday = $ordersQuery->whereDate('created_at', Carbon::today())->sum('DocTotal');

            $monthlyResults = $this->getSalesResults($table, $user, $startMONTH, $endMONTH, $SlpCode);
            $weeklyResults = $this->getSalesResults($table, $user, $startWeek, $endWeek, $SlpCode);

            $details = [
                'object' => $table->ObjectID,
                'documentName' => $table->DocumentName,
                'total' => $total,
                'totalValue' => $totalValue,
                'totalToday' => $totalToday,
                'totalValueToday' => $totalValueToday,
                'TotalMonthly' => $monthlyResults,
                'TotalWeekly' => $weeklyResults,
            ];

            $docSummary[] = $details;
        }

        return $docSummary;
    }

    private function getSalesResults($table, $user, $startDate, $endDate, $SlpCode)
    {
        $results = $table->ObjectHeaderTable::select(DB::raw('SUM(VatSum) as VatSum'), DB::raw('SUM(DocTotal) as TotalDocTotal'))
            ->when(!$user->SUPERUSER, function ($query) use ($user) {
                $query->where("UserSign", $user->id);
            })
            ->when($SlpCode, function ($query) use ($SlpCode) {
                $query->where("SlpCode",  $SlpCode);
            })
            ->where('CANCELED', '=', 'N')
            ->whereBetween('DocDate', [$startDate, $endDate])
            ->first();
        return ($results->TotalDocTotal  - $results->VatSum);
    }
}
