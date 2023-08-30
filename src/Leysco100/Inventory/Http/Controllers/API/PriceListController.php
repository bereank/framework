<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;


use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OPLN::with('basenum', 'PrimCurr', 'addcurr1', 'addcurr2')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = OPLN::create([
                'ListName' => $request['ListName'], //Price List Name
                'ValidFor' => $request['ValidFor'], //     Active
                'PrimCurr' => $request['PrimCurr'], // Primary Currency
                'BASE_NUM' => $request['BASE_NUM'], // Base Price List
                'Factor' => $request['Factor'], // Default Factor
                'AddCurr1' => $request['AddCurr1'], // Additional Currency
                'AddCurr2' => $request['AddCurr2'], // Additional Currency 2
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = OPLN::with('itm1.item')
                ->where('id', $id)
                ->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
        $details = [
            'ListName' => $request['ListName'],
            'ValidFor' => $request['ValidFor'],
            'PrimCurr' => $request['PrimCurr'],
            'BASE_NUM' => $request['BASE_NUM'], // Base Price List
            'Factor' => $request['Factor'], // Default Factor
            'AddCurr1' => $request['AddCurr1'],
            'AddCurr2' => $request['AddCurr2'],
        ];
        OPLN::where('id', $id)->update($details);

        return response()
            ->json(
                [
                    'message' => "Updated Successfully",
                ],
                201
            );
    }
}
