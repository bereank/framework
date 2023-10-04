<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Targets;
use Leysco100\Shared\Models\TargetItems;
use Leysco100\Shared\Models\TargetSetup;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\TargetSalesEmp;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Jobs\SalesTargetJob;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;


class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        $targetSetup = TargetSetup::with('document_lines', 'items');

        if (!$user->SUPERUSER) {
            $slpCode = $user->oudg->SalePerson ?? 0;
            $targetSetup = $targetSetup->whereHas('salesEmployees', function ($query) use ($slpCode) {
                $query->where('SlpCode', $slpCode);
            });
        }
        $targetSetup = $targetSetup->get();

        //  $targetSetup = TargetSetup::with('document_lines', 'items', 'salesEmployees')->get();

        return (new ApiResponseService())->apiSuccessResponseService($targetSetup);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPeriods($startDate, $endDate, $periodType)
    {
        $periods = [];
        $startYear = date('Y', strtotime($startDate));
        $endYear = date('Y', strtotime($endDate));

        for ($year = $startYear; $year <= $endYear; $year++) {
            switch ($periodType) {
                case 'M':
                    $startMonth = ($year == $startYear) ? date('n', strtotime($startDate)) : 1;
                    $endMonth = ($year == $endYear) ? date('n', strtotime($endDate)) : 12;
                    for ($month = $startMonth; $month <= $endMonth; $month++) {
                        $periodCode = $year . sprintf('%02d', $month);
                        $periodName = date('F Y', strtotime($year . '-' . $month . '-01'));
                        $startDate = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
                        $endDate = date('Y-m-t', strtotime($year . '-' . $month . '-01'));
                        $periods[] = ['code' => $periodCode, 'name' => $periodName, 'start_date' => $startDate, 'end_date' => $endDate];
                    }
                    break;
                case 'Q':
                    $startQuarter = ($year == $startYear) ? ceil(date('n', strtotime($startDate)) / 3) : 1;
                    $endQuarter = ($year == $endYear) ? ceil(date('n', strtotime($endDate)) / 3) : 4;
                    for ($quarter = $startQuarter; $quarter <= $endQuarter; $quarter++) {
                        $periodCode = $year . 'Q' . $quarter;
                        $periodName = 'Q' . $quarter . ' ' . $year;
                        $startMonth = ($quarter == 1) ? '01' : (($quarter == 2) ? '04' : (($quarter == 3) ? '07' : '10'));
                        $startDate = date('Y-m-d', strtotime($year . '-' . $startMonth . '-01'));
                        $endDate = date('Y-m-t', strtotime($year . '-' . ($startMonth + 2) . '-01'));
                        $periods[] = ['code' => $periodCode, 'name' => $periodName, 'start_date' => $startDate, 'end_date' => $endDate];
                    }
                    break;
                case 'A':
                    $periodCode = ('Y' . $year);
                    $periodName = ('Year' . $year);
                    $startDate = date('Y-m-d', strtotime($year . '-01-01'));
                    $endDate = date('Y-m-t', strtotime($year . '-12-01'));
                    $periods[] = ['code' => $periodCode, 'name' => $periodName, 'start_date' => $startDate, 'end_date' => $endDate];
                    break;
                case 'D':
                    $startTimestamp = strtotime($startDate);
                    $endTimestamp = strtotime($endDate);
                    while ($startTimestamp <= $endTimestamp) {
                        $periodCode = date('Ymd', $startTimestamp);
                        $periodName = date('F j, Y', $startTimestamp);
                        $startDate = date('Y-m-d', $startTimestamp);
                        $endDate = date('Y-m-d', $startTimestamp);
                        $periods[] = ['code' => $periodCode, 'name' => $periodName, 'start_date' => $startDate, 'end_date' => $endDate];
                        $startTimestamp += 86400;
                    }
                    break;
                case 'N':
                    $periodCode = $startDate . '_' . $endDate;
                    $periodName = $startDate;
                    $periods[] = ['code' => $periodCode, 'name' => $periodName, 'start_date' => $startDate, 'end_date' => $endDate];
                    break;
            }
        }

        return $periods;
    }

    public function store(Request $request)
    {

        $rules = [
            'employees' => 'required_without:selectAllISlP',
            'skus' => 'nullable|array',
            'Tvalue' => 'required|numeric',
            'EndDate' => 'required|date',
            'StartDate' => 'required|date',
            'TargetType' => 'nullable|string',
            'UoM' => 'nullable',
            'comment' => 'nullable|string',
            'RecurPat' => 'nullable|string',
        ];

        $messages = [

            'employees.required' => 'Please select at least one employee.',
            'Tvalue.required' => 'The target value is required.',
            'Tvalue.numeric' => 'The target value must be a number.',
            'EndDate.required' => 'Select end date.',
            'EndDate.date' => 'Select end date.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiFailedResponseService($validator->errors()->first());
        }
        $user = Auth::user();
        $HeaderDetails = TargetSetup::create([
            'TfromDate' => $request['StartDate'],
            //Target  From Date
            'TtoDate' => $request['EndDate'],
            //Target To Date
            'Comment' => $request['comment'] ?? "",
            'RecurPat' => $request['RecurPat'] ?? "",
            'UserSign' => $user->id,
        ]);
        // Add sales employees
        if ($request['selectAllISlP']) {
            $data = OSLP::select('SlpCode')
                ->orderBy('SlpCode', 'asc')
                ->get();
            foreach ($data as $key => $value) {
                $slp = TargetSalesEmp::create([
                    'target_setup_id' => $HeaderDetails->id,
                    'SlpCode' => $value['SlpCode']
                ]);
            }
        } else {
            foreach ($request['employees'] as $key => $value) {
                $slp = TargetSalesEmp::create([
                    'target_setup_id' => $HeaderDetails->id,
                    'SlpCode' => $value
                ]);
            }
        }
        //create periods
        $periods = $this->getPeriods($request['StartDate'], $request['EndDate'], $request['RecurPat']);
        foreach ($periods as $key => $val) {
            Log::info($key, $val);
            $TargetRows = Targets::create([
                'target_setup_id' => $HeaderDetails->id,
                'UoM' => $request['UoM'] ?? null,
                //Target Metric
                'Tvalue' => $request['Tvalue'],
                'TargetType' => $request['TargetType'] ?? null,
                //O--OUOM,M--Monetory
                'PeriodStart' => $val['start_date'],
                'PeriodEnd' => $val['end_date'],
                'TCode' => $val['code'],
                'TName' => $val['name'],
            ]);
        }

        if ($request['selectAllItems']) {
            $data = OITM::select('id', 'ItemCode')
                ->where('SellItem', 'Y')
                ->orderBy('ItemCode', 'asc')
                ->get();

            $items = $data;

            // Dispatch the job
            dispatch(new SalesTargetJob($items, $request['UoM'], $HeaderDetails->id));
        } else {
            //Creating Rows:
            foreach ($request['skus'] as $key => $item) {
                $Items = TargetItems::create([
                    'UoM' => $request['UoM'] ?? "",
                    //Target Metric
                    'ItemCode' => $item,
                    //Target Val
                    'target_setup_id' => $HeaderDetails->id,
                    //User
                ]);
            }
        }

        return (new ApiResponseService())->apiSuccessResponseService("success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $targt = Targets::with('setup')->where('target_setup_id', $id)->get();
        return (new ApiResponseService())->apiSuccessResponseService($targt);
    }

    public function getEmpTargets()
    {

        $sales_target = Targets::with('document_lines.oitm', 'user', 'employees', 'metrics', 'invoices')->get();

        return (new ApiResponseService())->apiSuccessResponseService($sales_target);
    }
    public function salesRepsTargets()
    {
        $targets = Targets::get();
        $collection = collect();

        foreach ($targets as $target) {
            $results = Targets::with([
                'invoices' => function ($query) use ($target) {
                    $query->select('id', 'SlpCode', 'VatSum', 'DocDate', DB::raw('SUM(DocTotal) as TotalDocTotal'))
                        ->whereBetween('DocDate', [
                            Carbon::parse($target->TfromDate)->subDay(),
                            Carbon::parse($target->TtoDate)->addDay(),
                        ])
                        ->where('CANCELED', "=", 'N');
                }
            ])
                ->with('employees')
                ->with('document_lines')
                ->where('id', $target->id)
                ->get();

            $collection = $collection->merge($results);
        }

        $flattened = $collection->flatten();

        $arr = [];
        foreach ($flattened as $flat) {
            $total = $flat->invoices->first();
            $target = $flat->document_lines->first();
            if ($target['Tvalue'] == 0) {
                $result = 0;
            } else {
                $result = $target['Tvalue'] / 30;
            }
            $todaySalesToday = OINV::where('SlpCode', $flat->SlpCode)
                ->whereDate('DocDate', Carbon::today())
                ->sum('DocTotal');


            array_push(
                $arr,
                [
                    "SlpCode" => $flat->SlpCode ?? 0,
                    "RecurPat" => $flat->RecurPat ?? null,
                    'SlpName' => $flat->employees->SlpName ?? null,
                    "Tvalue" => $target['Tvalue'],
                    'TotalSales' => $total["TotalDocTotal"] ?? 0,
                    'Comment' => $flat->Comment ?? null,
                    'AchievementRatio' => isset($total["TotalDocTotal"]) ? (($total["TotalDocTotal"] / $target['Tvalue']) * 100) : 0.00,
                    'Dailytarget' => $result ?? 0,
                    'TodaySales' => $todaySalesToday ?? 0,

                ],
            );
        }
        return (new ApiResponseService())->apiSuccessResponseService($arr);
    }
    public function showSkus($id)
    {

        $skus = TargetItems::with('oitm', 'setup.document_lines')->where('target_setup_id', $id)->get();
        return (new ApiResponseService())->apiSuccessResponseService($skus);
    }

    public function getTargetItems($id)
    {
        $TargetItems = Targets::with('items.oitm', 'setup.employees')->where('id', $id)->first();
        return (new ApiResponseService())->apiSuccessResponseService($TargetItems);
    }

    public function getTargetEmployeese($id)
    {

        $Targetspl = TargetSalesEmp::with('setup.document_lines', 'employees')->where('target_setup_id', $id)->get();

        return (new ApiResponseService())->apiSuccessResponseService($Targetspl);
    }
    public function removeTargetSlp($id)
    {

        TargetSalesEmp::where('id', $id)->delete();
        return (new ApiResponseService())->apiSuccessResponseService('success');
    }
    public function addSlpToTarget(Request $request)
    {
        $rules = [
            'target_setup_id' => 'required',
            'employees' => 'array|required',
            'employees.*' => 'nullable',
        ];

        $messages = [
            'target_setup_id.exists' => 'The selected target setup is invalid.',
            'employees.*.exists' => 'The selected sales employee is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiFailedResponseService($validator->errors()->first());
        }

        try {
            foreach ($request['employees'] as $item) {
                TargetSalesEmp::create([
                    'target_setup_id' => $request['target_setup_id'],
                    'SlpCode' => $item
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService("Added Sucessfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getTargetsVsPerfomance()
    {
        //  try {
        $slpCode = null;
        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        if (!$user->SUPERUSER) {
            $slpCode = $user->oudg->SalePerson ?? 0;
        }
        ;
        $startdate = request()->filled('startdate') ? Carbon::parse(request()->input('startdate'))->startOfDay() : Carbon::now()->startOfMonth();

        $endate = request()->filled('enddate') ? Carbon::parse(request()->input('enddate'))->endOfDay() : Carbon::now()->endOfMonth();

        $isSingleSlp = false;
        $slpCodes = [];
        if (request()->filled('SlpCodes')) {
            $slpCodes = explode(',', request()->input('SlpCodes'));
            $isSingleSlp = true;
        }

        // $res = [];
        //   return $slpCodes;
        // foreach ($slpCodes as $slpCode) {
        // $slpCode = 0;

        $target_items = DB::connection("tenant")->table('target_items')
            //->groupby('target_setup_id')
            ->select('target_setup_id', 'ItemCode');
        //, DB::raw("GROUP_CONCAT(`ItemCode` SEPARATOR ',') as `ItemCode`"));

        $filteredtargets = DB::connection("tenant")->table('targets')->whereBetween('PeriodStart', [
            $startdate,
            $endate,
        ])
            ->whereBetween('PeriodEnd', [
                $startdate,
                $endate,
            ])
            ->select('target_setup_id', 'Tvalue', 'PeriodStart', 'PeriodEnd');


        //Invoices Start
        $validinvoices = DB::connection("tenant")->table('o_i_n_v_s')->where('CANCELED', "=", 'N')->select('id', 'SlpCode', 'DocDate')->whereBetween('DocDate', [
            $startdate,
            $endate,
        ])
            ->whereBetween('DocDate', [
                $startdate,
                $endate,
            ]);


        $invoicelines = DB::connection("tenant")->table('i_n_v1_s')
            ->whereBetween('i_n_v1_s.DocDate', [
                $startdate,
                $endate,
            ])

            ->joinSub($validinvoices, 'invoices', function ($join) {
                $join->on("invoices.id", '=', 'i_n_v1_s.DocEntry');
            })
            ->groupBy('invoices.SlpCode')
            ->groupBy('i_n_v1_s.ItemCode')
            ->groupBy('i_n_v1_s.DocDate')
            ->groupBy('invoices.DocDate')
           
            ->select(
                'i_n_v1_s.ItemCode',
                'invoices.DocDate',
                'invoices.SlpCode',
                DB::connection("tenant")->raw("SUM(i_n_v1_s.LineTotal) as LineTotal"),
                DB::connection("tenant")->raw("SUM(i_n_v1_s.GTotal) as GTotal"),
                DB::connection("tenant")->raw("SUM(i_n_v1_s.Quantity) as Quantity")
            );
        // Invoices End

      //  Credit-Notes start
            $validCreditNotes  = DB::connection("tenant")->table('o_r_i_n_s')->where('CANCELED', "=", 'N')->select('id', 'SlpCode', 'DocDate')->whereBetween('DocDate', [
                $startdate,
                $endate,
            ])
                ->whereBetween('DocDate', [
                    $startdate,
                    $endate,
                ]);
    

            $creditNotelines  = DB::connection("tenant")->table('r_i_n1_s')
            ->whereBetween('r_i_n1_s.DocDate', [
                $startdate,
                $endate,
            ])
            ->joinSub($validCreditNotes, 'credit_notes', function ($join) {
                $join->on("credit_notes.id", '=', 'r_i_n1_s.DocEntry');
            })
            ->groupBy('credit_notes.SlpCode')
            ->groupBy('r_i_n1_s.ItemCode')
            ->groupBy('r_i_n1_s.DocDate')
            ->groupBy('credit_notes.DocDate')
            ->select(
                'r_i_n1_s.ItemCode',
                'credit_notes.DocDate',
                'credit_notes.SlpCode',
                DB::connection("tenant")->raw("SUM(r_i_n1_s.LineTotal) as CreditNoteLineTotal"),
                DB::connection("tenant")->raw("SUM(r_i_n1_s.GTotal) as CreditNoteGTotal"),
                DB::connection("tenant")->raw("SUM(r_i_n1_s.Quantity) as CreditNoteQuantity")
            );
        //Credit Notes End
        $target_row = DB::connection("tenant")->table('target_sales_emps')
            ->join('o_s_l_p_s', 'o_s_l_p_s.SlpCode', '=', 'target_sales_emps.SlpCode')

            ->when($isSingleSlp, function ($query) use ($slpCodes) {
                $query->whereIn('target_sales_emps.SlpCode', $slpCodes);
            })
            ->when(!$user->SUPERUSER, function ($query) use ($slpCode) {
                $query->where('target_sales_emps.SlpCode', $slpCode);
            })
            ->joinSub($filteredtargets, 'filteredtargets', function ($join) {
                $join->on("filteredtargets.target_setup_id", '=', 'target_sales_emps.target_setup_id');
            })
            ->join('target_setups', 'filteredtargets.target_setup_id', '=', 'target_setups.id')
            ->joinSub($target_items, 'items', function ($join) {
                $join->on("filteredtargets.target_setup_id", '=', 'items.target_setup_id');
            })

            ->leftJoinSub($invoicelines, 'invoices', function ($join) {
                $join->on("target_sales_emps.SlpCode", '=', 'invoices.SlpCode');
                $join->on("invoices.ItemCode", "items.ItemCode");
                $join->on(DB::connection("tenant")->raw('invoices.DocDate BETWEEN filteredtargets.PeriodStart AND filteredtargets.PeriodEnd'), DB::raw("TRUE"));
            })

            ->leftJoinSub($creditNotelines, 'creditNotes', function ($join) {
                $join->on("target_sales_emps.SlpCode", '=', 'creditNotes.SlpCode');
                $join->on("creditNotes.ItemCode", "items.ItemCode");
                $join->on(DB::connection("tenant")->raw('creditNotes.DocDate BETWEEN filteredtargets.PeriodStart AND filteredtargets.PeriodEnd'), DB::raw("TRUE"));
            })

            //->groupBy('items.ItemCode')
            ->groupBy('target_sales_emps.SlpCode')
            ->groupBy('o_s_l_p_s.SlpName')
            ->groupBy('filteredtargets.Tvalue')
            ->groupBy('filteredtargets.PeriodStart')
            ->groupBy('filteredtargets.PeriodEnd')
            ->groupBy('target_setups.Comment')

            ->select(
                'target_setups.Comment',
                //'items.ItemCode',
                'o_s_l_p_s.SlpName',
                'target_sales_emps.SlpCode',
                'filteredtargets.Tvalue',
                'filteredtargets.PeriodStart',
                'filteredtargets.PeriodEnd',
                //DB::raw("GROUP_CONCAT(items.ItemCode SEPARATOR ',') as ItemCode"),
                DB::connection("tenant")->raw('COALESCE(ROUND(SUM(invoices.LineTotal) / filteredtargets.Tvalue * 100, 2),0) AS line_total_percentage'),
                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(invoices.LineTotal), 2), 0) AS LineTotal"),
                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(invoices.GTotal), 2), 0) AS GTotal"),
                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(invoices.Quantity), 2), 0) AS Quantity"),

                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(creditNotes.CreditNoteLineTotal), 2), 0) AS CreditNoteLineTotal"),
                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(creditNotes.CreditNoteGTotal), 2), 0) AS CreditNoteGTotal"),
                DB::connection("tenant")->raw("COALESCE(ROUND(SUM(creditNotes.CreditNoteQuantity), 2), 0) AS CreditNoteQuantity")

            )
        ->get();
        //     array_push($res, $target_row);
        // }
        //  return ($target_row);

       // ->toSql();
        // $sql =  Str::replaceArray('?', $target_row->getBindings(), $target_row->toSql());
        // Log::info($sql);
        //     // Your Eloquent query executed by using get()

        //Log::info(DB::getQueryLog());
        return (new ApiResponseService())->apiSuccessResponseService($target_row);
        // } catch (\Throwable $th) {
        //     return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        // }
    }
    public function getEmployeesTargets($id)
    {

        $user = User::where('id', Auth::user()->id)->with('oudg')->first();

        try {
            $target_row = Targets::findorFail($id);

            $sales_employees = TargetSalesEmp::where('target_setup_id', $target_row['target_setup_id'])->with('employees');

            if (!$user->SUPERUSER) {
                $slpCode = $user->oudg->SalePerson ?? 0;
                $sales_employees = $sales_employees->where('SlpCode', $slpCode);
            }
            ;

            $sales_employees = $sales_employees->get();

            $target_items = TargetItems::where('target_setup_id', $target_row['target_setup_id'])->get()->pluck('ItemCode')->toArray();

            $sales_employees = $sales_employees->map(function ($employee) use ($target_row, $target_items) {
                $achievement = $this->slpAchievement($employee['SlpCode'], $target_row['PeriodStart'], $target_row['PeriodEnd'], $target_items);
                $employee['totalQuantitySold'] = $achievement['totalQuantity'] ?? 0;
                $employee['totalAmountSold'] = $achievement['totalAmount'] ?? 0;
                $employee['Tvalue'] = $target_row['Tvalue'] ?? 0;
                $employee['target_row'] = $target_row;
                // if ($target_row['TargetType'] == "Q") {
                //     $employee['achievement_quantity_percentage'] =  ($employee['Tvalue'] && $achievement['totalQuantity']) ? ($achievement['totalQuantity'] / $target_row['Tvalue'] * 100) : 0;

                //     $employee['achievement_quantity_percentage'] = number_format($employee['achievement_quantity_percentage'], 2);
                // }
                // if ($target_row['TargetType'] == "A") {
                $employee['achievement_amount_percentage'] = ($employee['Tvalue'] && $achievement['totalAmount']) ? ($achievement['totalAmount'] / $target_row['Tvalue'] * 100) : 0;

                $employee['achievement_amount_percentage'] = number_format($employee['achievement_amount_percentage'], 2);
                // }


                return $employee;
            });

            return (new ApiResponseService())->apiSuccessResponseService($sales_employees);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function slpAchievement($slpCode, $periodStart, $periodEnd, $items)
    {
        $invoices = OINV::where('CANCELED', "=", 'N')
            ->whereHas('document_lines', function ($query) use ($items) {
                $query->whereIn('ItemCode', $items);
            })
            ->whereBetween('DocDate', [
                Carbon::parse($periodStart)->startOfDay(),
                Carbon::parse($periodEnd)->endOfDay(),
            ])
            ->where('SlpCode', $slpCode)
            ->with([
                'document_lines' => function ($query) use ($items) {
                    $query->whereIn('ItemCode', $items)
                        ->select(["id", 'ItemCode', 'DocEntry', 'LineTotal', 'Dscription', 'Price', 'Quantity']);
                }
            ])
            ->select('id', 'SlpCode', 'DocTotal', 'DocNum', 'VatSum', 'DocDate')
            ->get();

        $totalAmount = $invoices->sum('DocTotal');

        $totalQuantity = $invoices->flatMap(function ($invoice) {
            return $invoice['document_lines'];
        })->sum('Quantity');

        return ['totalQuantity' => $totalQuantity, 'totalAmount' => $totalAmount];
    }

    public function ItemsData()
    {
        try {
            $data = OITM::select('id', 'ItemName', 'ItemCode')
                ->where('SellItem', 'Y')
                ->orderBy('ItemCode', 'asc')
                ->take(2000)
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}