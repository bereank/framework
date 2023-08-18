<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Financials;

use Auth;
use Illuminate\Http\Request;

use App\Imports\GLAccountImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ChartOfAccount::whereNull('chart_of_account_id')
                ->with('children')
                ->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function fetchActiveAccounts()
    {
        $type = \Request::get('type');

//        if ($type) {
//            if ($type == "Revenue") {
//                $accounts = DB::select('call REVENUE_ACCOUNTS()');
//                return $accounts;
//            }
//
//            if ($type == "Checks") {
//                $accounts = DB::select('call CHECKS_ACCOUNTS()');
//                return $accounts;
//            }
//        }

        return ChartOfAccount::select('id', 'AcctCode', 'Postable', 'AcctName', 'Finanse')
            ->where('Frozen', 'N')
            ->where('Postable', 'Y')
            ->get();
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'AcctName' => 'required',
            'AcctCode' => 'required',
            'Levels' => 'required',
            'chart_of_account_id' => 'required',
        ]);

        try {
            $user = Auth::user();
            $Checked = $request['checked'];
            if ($Checked == true) {
                $postable = 'N';
            } else {
                $postable = 'Y';
            }
            $data = ChartOfAccount::create([
                'AcctName' => $request['AcctName'],
                'AcctCode' => $request['AcctCode'],
                'Postable' => $postable,
                'Levels' => $request['Levels'],
                'chart_of_account_id' => $request['chart_of_account_id'],
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
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
        $data = ChartOfAccount::where('id', $id)
            ->whereNull('chart_of_account_id')
            ->with('children')
            ->get();

        $AllData = ChartOfAccount::
            // whereNull('chart_of_account_id')
            where('Postable', 'N')
            // ->with('childrenRecursive')
            ->get();

        return response()->json([
            'data' => $data,
            'data1' => $AllData,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = ChartOfAccount::findOrFail($id);
        $user->delete();
    }

    public function importGLAccount(Request $request)
    {
        $array = Excel::toCollection(new GLAccountImport(), request()->file('excel'));
        foreach ($array as $key => $value) {
            foreach ($value as $key => $row) {
                $AccountCode = $row[0];
                $AccountName = $row[1];
                $CurrentBalance = $row[2];
                $MainGroup = $row[3];
                $Postable = $row[4];
                $AccountLevel = $row[5];
                $ParentAccountCode = $row[6];
                if ($ParentAccountCode != "") {
                    $chart_of_account_id = ChartOfAccount::where('AcctCode', $ParentAccountCode)->value('id');
                    $bene = ChartOfAccount::firstOrCreate(
                        [
                            'AcctCode' => $AccountCode,
                            'AcctName' => $AccountName
                        ],
                        [
                            'Postable' => $Postable,
                            'Levels' => $AccountLevel,
                            'chart_of_account_id' => $chart_of_account_id
                        ]
                    );
                } else {
                    $bene = ChartOfAccount::firstOrCreate(['AcctCode' => $AccountCode, 'AcctName' => $AccountName], [
                        'Postable' => $Postable,
                        'Levels' => $AccountLevel
                    ]);
                }
            }
            return "Done";
        }
    }
}
