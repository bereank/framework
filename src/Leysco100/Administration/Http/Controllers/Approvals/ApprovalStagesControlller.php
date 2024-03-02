<?php

namespace Leysco100\Administration\Http\Controllers\Approvals;

use Illuminate\Http\Request;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\OWST;
use Leysco100\Shared\Models\Administration\Models\WST1;
use Leysco100\Shared\Services\ApiResponseService;

class ApprovalStagesControlller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stages = OWST::with('wst1.users')->get();

        return (new ApiResponseService())->apiSuccessResponseService($stages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            //Approval Stages
            $Header = OWST::create([
                'WstCode' => $request['WstCode'], //Stage
                'Name' => $request['Name'], //Name
                'Remarks' => $request['Remarks'], //Description
                'MaxReqr' => $request['MaxReqr'], //    No. of Authorizers
                'MaxRejReqr' => $request['MaxRejReqr'], //No. of Rejects
            ]);

            //ApprovalS Stages document_lines
            foreach ($request['users'] as $key => $value) {
                $items = WST1::create([
                    'WstCode' => $Header->id, //Stage
                    'UserID' => $value,
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService($Header);
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
            $data = OWST::with('wst1.users')
                ->where('id', $id)
                ->first();
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
            $owst = OWST::where('id', $id)->first();
            $detailsHeader = [
                'WstCode' => $request['WstCode'], //Stage
                'Name' => $request['Name'], //Name
                'Remarks' => $request['Remarks'], //Description
                'MaxReqr' => $request['MaxReqr'], //    No. of Authorizers
                'MaxRejReqr' => $request['MaxRejReqr'], //No. of Rejects
            ];
            $owst->update($detailsHeader);

            $AllUsers = WST1::where('WstCode', $id)->get();
            $users = $request['users'];

            foreach ($AllUsers as $key => $value) {
                if (!in_array($value->id, $users)) {
                    WST1::where('id', $value->id)->delete();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService();
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
        //
    }
}
