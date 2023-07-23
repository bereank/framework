<?php

namespace App\Http\Controllers\API\Administration\Setup\Financials\GLDetermination;

use App\Domains\Administration\Models\OADM;
use App\Domains\Finance\Models\ACP10;
use App\Domains\Finance\Models\ChartOfAccount;
use App\Domains\Finance\Models\OACP;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GLAccountDeterminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OADM::with('opln')->where('id', 1)->first();
            $data1 = OACP::with('acp10.glaccount')->where('id', 1)
                ->first();

            if ($data1) {
                $allData = array_merge($data->toArray(), $data1->toArray());
            } else {
                $allData = $data;
            }
            return (new ApiResponseService())
                ->apiSuccessResponseService($allData);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $details = [
                'CostPrcLst' => $request['CostPrcLst'], //Based Price Origin
                'DfSVatItem' => $request['DfSVatItem'], //Sales Tax Groups Items
                'DfSVatServ' => $request['DfSVatServ'], //Sales Tax Groups Service
                'DfPVatItem' => $request['DfPVatItem'], //Purchase Tax Groups Items
                'DfPVatServ' => $request['DfPVatServ'], //Purchaes Tax Groups Items
            ];
            OADM::where('id', 1)->update(array_filter($details));

            foreach ($request['acp10'] as $key => $value) {
                $listdetals = [
                    'Code' => $value['Code'],
                    'AcctCode' => $value['AcctCode'],
                    'AcctName' => $value['AcctName'],
                ];

                ACP10::where('id', $value['id'])->update($listdetals);
            }

            DB::commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::rollback();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAccountPerCategory($category)
    {
        try {
            $data = ACP10::where('Category', $category)
                ->where('PrdCtgyCode', 1)->get();
            foreach ($data as $key => $value) {
                $oact = ChartOfAccount::where('id', $value->AcctCode)->first();
                $value->Code = $oact ? $oact->AcctCode : null;
                $value->AcctName = $oact ? $oact->AcctName : null;
            }
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
