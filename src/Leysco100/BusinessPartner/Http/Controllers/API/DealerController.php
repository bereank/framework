<?php

namespace App\Http\Controllers\API\BusinessPartner;




use Illuminate\Http\Request;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\ODRD;
use Leysco100\BusinessPartner\Http\Controllers\Controller;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ODRD::get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getBranches()
    {
        try {
            $data = OBPL::first()->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
            $dealer = ODRD::create([
                'Name' => $request['Name'],
                'Phone' => $request['Phone'],
                'Type' => $request['Type'],
                'BPLId' => $request['BPLId'],
                'LocationAbrn' => $request['LocationAbrn'],
                'Town' => $request['Town'],
                'County' => $request['County'],
                'Discount' => $request['Discount'],
                'VisOrder' => $request['VisOrder'],
            ]);
            return (new ApiResponseService())->apiSuccessResponseService();
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
            $data = ODRD::where('id', $id)->first();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
