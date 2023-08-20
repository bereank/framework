<?php

namespace App\Http\Controllers\API\Financials;

use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addingUser = \Request::get('addingUser');
        try {
            $data = OBPL::with('location')->get();
            if ($addingUser) {
                $AllBranches = array(
                    'BPLName' => "All Branches",
                    'id' => -1,
                );
                $data->push($AllBranches);
            }
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = OBPL::create([
                'LocationCode' => $request['LocationCode'],
                'BPLName' => $request['BPLName'],
                'ExtRef' => $request['ExtRef'],
            ]);
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
        try {
            $data = OBPL::where('id', $id)->first();
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
        DB::beginTransaction();
        try {
            $branch = OBPL::where('id', $id)->first();
            $branch->update([
                'LocationCode' => $request['LocationCode'],
                'BPLName' => $request['BPLName'],
                'ExtRef' => $request['ExtRef'],
            ]);
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService($branch);
        } catch (\Throwable $th) {
            DB::rollback();
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
