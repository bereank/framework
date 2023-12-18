<?php

namespace Leysco100\Shared\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Shared\Models\ORCL;
use Leysco100\Shared\Models\Shared\Models\ORCP;
use Leysco100\Shared\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\RecurrPeriodsService;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OEDG;



class RecurringTransactionsTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return ORCP::with(
        //     'odrf',
        //     'odrf.drf1.oitm.itm1',
        //     'odrf.drf1.oitm.inventoryuom',
        //     'odrf.drf1.oitm.ougp.ouom',
        //     'odrf.drf1.oitm.oitb'
        // )
        //     ->get();
        try {
            $data = ORCP::with('orcl')->where('IsRemoved', 'N')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function pendingTransactions()
    {
        return ORCP::with('orcl', 'odrf.objecttype')
            ->whereHas('orcl', function ($q) {
                $q->whereDate('PlanDate', '<=', date("Y-m-d"))
                    ->where('Status', 'N');
            })
            ->get();
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

        try {
            $this->validate($request, [
                'Code' => 'unique:tenant.o_r_c_p_s,Code',
                'Dscription' => 'required',
                'DocObjType' => 'required',
                'DraftEntry' => 'required',
                'StartDate' => 'required',
                'EndDate' => 'required',
            ]);
            $newAgrrement = ORCP::create([
                'Code' => $request['Code'], //Template
                'Dscription' => $request['Dscription'], //Description
                'Frequency' => $request['Frequency'], //Recuurence Period A=Annually, D=Daily, M=Monthly, O=One Time, Q=Quarterly, S=Semiannually, W=Weekly
                'DocObjType' => $request['DocObjType'], //Type
                'Remind' => $request['Remind'], // Sub-Frequency
                'StartDate' => $request['StartDate'], //Start Date
                'EndDate' => $request['EndDate'], //End Date
                'DraftEntry' => $request['DraftEntry'], //Doc No.
                'ExecDay' => $request['ExecDay'],
                'ExecTime' => $request['ExecTime'],
            ]);
            //Creating Recurring Transaction Templates INstances
            // $dt = new Carbon($request['StartDate']);
            // if ($request['Frequency'] == "D") {
            //     $planDate = $dt->addDay()->toDateTimeString();
            // } elseif ($request['Frequency'] == "W") {
            //     $planDate = $dt->addWeek()->toDateTimeString();
            // } elseif ($request['Frequency'] == "M") {
            //     $planDate = $dt->addMonth()->toDateTimeString();
            // } elseif ($request['Frequency'] == "A") {
            //     $planDate = $dt->addYear()->toDateTimeString();
            // } elseif ($request['Frequency'] == "Q") {
            //     $planDate = $dt->addMonth(4)->toDateTimeString();
            // } elseif ($request['Frequency'] == "S") {
            //     $planDate = $dt->addMonth(6)->toDateTimeString();
            // }
            // $newAgrrement = ORCL::create([
            //     'RcpEntry' => $newAgrrement->id, //Template Entry
            //     'PlanDate' => $request['ExecDay'] . ' ' . $request['ExecTime'],
            //     'DocObjType' => $request['DocObjType'],
            //     'Instance' => $request['Instance'] ?? 1,
            // ]);

            return (new ApiResponseService())->apiSuccessResponseService($newAgrrement);
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
            $data = ORCP::with('orcl')->where('id', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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

        try {
            $this->validate($request, [
                'Code' => 'unique:tenant.o_r_c_p_s,Code',
                'Dscription' => 'required',
                'DocObjType' => 'required',
                'DraftEntry' => 'required',
                'StartDate' => 'required',
                'EndDate' => 'required',
            ]);
            $newAgrrement = ORCP::where('id', $id)->update([
                'Code' => $request['Code'], //Template
                'Dscription' => $request['Dscription'], //Description
                'Frequency' => $request['Frequency'], //Recuurence Period A=Annually, D=Daily, M=Monthly, O=One Time, Q=Quarterly, S=Semiannually, W=Weekly
                'DocObjType' => $request['DocObjType'], //Type
                'Remind' => $request['Remind'], // Sub-Frequency
                'StartDate' => $request['StartDate'], //Start Date
                'EndDate' => $request['EndDate'], //End Date
                'DraftEntry' => $request['DraftEntry'], //Doc No.
                'ExecDay' => $request['ExecDay'],
                'ExecTime' => $request['ExecTime'],
            ]);


            return (new ApiResponseService())->apiSuccessResponseService($newAgrrement);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = ORCP::where('id', $id)->update([
                'IsRemoved' => 'Y'
            ]);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function getNextExec(Request $request)
    {
    
        try {
            $recurrPeriodsService = new RecurrPeriodsService();
            $NextExecution = $recurrPeriodsService
                ->processPeriod(
                    Frequency: $request['Frequency'],
                    Remind: $request['Remind'] - 1000,
                    ExecTime: $request['ExecTime'],
                    StartDate: $request['StartDate'],
                    EndDate: $request['EndDate'],
                );
        
            return (new ApiResponseService())->apiSuccessResponseService($NextExecution);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
