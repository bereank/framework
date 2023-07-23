<?php

namespace App\Http\Controllers\API\Administration\Setup\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\InventoryAndProduction\Models\OITG;
use App\Domains\Shared\Services\ApiResponseService;

class ItemPropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = OITG::with('itg1')->get();

        return (new ApiResponseService())->apiSuccessResponseService($data);
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
        if (!$request['ItmsGrpNam']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Group Name Required");
        }

        //Check if there is a record
        $data = OITG::where('ItmsGrpNam', $request['ItmsGrpNam'])
            ->where('ItmsTypCod', $request['ItmsTypCod'])
            ->first();
        if ($data) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Property with those details Exist");
        }

        //Inserting
        try {
            $data = OITG::create(
                [
                'ItmsGrpNam' => $request['ItmsGrpNam'],
                'ItmsTypCod' => $request['ItmsTypCod'],
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
        $data = OITG::with('itg1')
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

    public function itemDesc(Request $request)
    {
        //Name Validation
        if (!$request['GrpName'] || !$request['ItmsTypCod']) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Group Name Required");
        }

        //Check if there is a record
        $data = ITG1::where('GrpName', $request['GrpName'])
            ->where('ItmsTypCod', $request['id'])
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
            $data = ITG1::create(
                [
                'GrpName' => $request['GrpName'],
                'ItmsTypCod' => $request['ItmsTypCod'],
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
