<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Leysco100\Shared\Models\Shared\Models\APDI;

class DocModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isDoc = \Request::get('isDoc');
        $module = \Request::get('Module');

        if ($module == "Service") {
            return APDI::whereIn('ObjectID', [17, 15, 16, 13, 14])
                ->get();
        }

        return APDI::with('pdi1')
            ->where('ObjectID', '!=', 112)
            ->whereIn('ObjectID', [17, 15, 16, 13, 14, 24, 205, 66, 67, 176, 189])
            ->where(function ($q) use ($isDoc) {
                if ($isDoc) {
                    $q->where('isDoc', "Y");
                }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return APDI::with('pdi1')
            ->where('ObjectID', $id)
            ->first();
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
            $detaisl = [
                'RowStatus' => $request['RowStatus'],
                'hasExtApproval' => $request['hasExtApproval'] == 1 ? 0 : 1,
            ];
            APDI::where('ObjectID', $id)->update($detaisl);
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
