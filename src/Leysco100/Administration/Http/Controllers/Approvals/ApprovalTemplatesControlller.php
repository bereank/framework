<?php

namespace Leysco100\Administration\Http\Controllers\Approvals;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\OWTM;
use Leysco100\Shared\Models\Administration\Models\WTM1;
use Leysco100\Shared\Models\Administration\Models\WTM2;
use Leysco100\Shared\Models\Administration\Models\WTM3;
use Leysco100\Shared\Models\Administration\Models\WTM4;
use Leysco100\Shared\Services\ApiResponseService;

class ApprovalTemplatesControlller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates =  OWTM::with('wtm1', 'wtm3')->get();

        return (new ApiResponseService())->apiSuccessResponseService($templates);
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

        //1. Validating Originators
        if (empty($request['wtm1'])) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Originators Data Required");
        }
        // 2. Approval Stages
        if (empty($request['wtm2'])) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Approval Stages Required");
        }

        // 2. Document
        if (empty($request['wtm3'])) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Documents Required");
        }

        DB::connection("tenant")->beginTransaction();
        try {
            $Header = OWTM::create([
                'Name' => $request['Name'], //Names
                'Remarks' => $request['Remarks'], //Desctriptions
                'Conds' => $request['Conds'], //Conditions Y,N
                'Active' => $request['Active'], //Status Y,N
                'PmptChg' => $request['PmptChg'], //Prompt Change
                'UserSign' => Auth::user()->id,
                'AppOnUpd' => $request['AppOnUpd'], // Apply on Update
            ]);
            //1. Approval Templates - Producers//Originators
            foreach ($request['wtm1'] as $key => $value) {
                $Header = WTM1::create([
                    'WtmCode' => $Header->id, //Stage
                    'UserID' => $value,
                ]);
            }
            //2. Confirmation Templates - Stages
            foreach ($request['wtm2'] as $key => $value) {
                $Header = WTM2::create([
                    'WtmCode' => $Header->id, //Stage, Approcal Template ID Header
                    'WstCode' => $value['WstCode'], //Stage ID
                    'SortId' => $value['SortId'],
                    'Remarks' => $value['Remarks'],
                ]);
            }
            //3. Approval Templates - Documents
            foreach ($request['wtm3'] as $key => $value) {
                $Header = WTM3::create([
                    'WtmCode' => $Header->id, //Stage, Approcal Template ID Header
                    'TransType' => $value, // Object ID
                ]);
            }
            //4. Approval Templates - Conditions
            foreach ($request['wtm4'] as $key => $value) {
                $Header = WTM4::create([
                    'WtmCode' => $Header->id, //Stage, Approcal Template ID Header
                    'CondId' => $value['CondId'], //Condition No.
                    'opCode' => $value['opCode'], //Condition No.
                    'opValue' => $value['opValue'], //Condition No.
                ]);
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
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
            $data = OWTM::with('wtm1', 'wtm2', 'wtm3')
                ->where('id', $id)
                ->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::rollback();
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
}
