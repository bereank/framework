<?php

namespace App\Http\Controllers\API\Administration\Setup\BusinessPartners;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Domains\BusinessPartner\Models\CQG1;
use App\Domains\BusinessPartner\Models\OCQG;
use App\Domains\Shared\Services\ApiResponseService;

class BPPropertiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OCQG::with('cqg1')->get();
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

        //Name Validation
        if (!$request['GroupName']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Group Name Required");
        }

        //Check if there is a record
        $data = OCQG::where('GroupName', $request['GroupName'])
            ->where('GroupCode', $request['GroupCode'])
            ->first();
        if ($data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Property with those details Exist");
        }

        //Inserting
        try {
            $data = OCQG::create(
                [
                'GroupName' => $request['GroupName'],
                'GroupCode' => $request['GroupCode'],
            ]
            );
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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
        $data = OCQG::with('cqg1')
            ->where('id', $id)
            ->first();
        if (!$data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Item Does not exist");
        }
        try {
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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
        $data = OCQG::with('cqg1')
            ->where('id', $id)
            ->first();
        if (!$data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Item Does not exist");
        }

        //Check if there is a record
        $existRecord = OCQG::where('GroupName', $request['GroupName'])
            ->where('GroupCode', $request['GroupCode'])
            ->where('id', '!=', $id)
            ->first();
        if ($existRecord) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Property with those details Exist");
        }

        //Inserting
        try {
            $data->update(
                [
                'GroupName' => $request['GroupName'],
                'GroupCode' => $request['GroupCode'],
            ]
            );
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
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

    public function propertDesc(Request $request)
    {
        //Name Validation
        if (!$request['Name'] || !$request['GroupCode']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Group Name Required");
        }

        //Check if there is a record
        $data = CQG1::where('Name', $request['Name'])
            ->where('GroupCode', $request['id'])
            ->first();
        if ($data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Property with those details Exist");
        }

        /**
         *  Creating Business Property
         *
         */
        try {
            $data = CQG1::create(
                [
                'Name' => $request['Name'],
                'GroupCode' => $request['GroupCode'],
            ]
            );
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())
                ->apiFailedResponseService($th->getMessage());
        }
    }
}
