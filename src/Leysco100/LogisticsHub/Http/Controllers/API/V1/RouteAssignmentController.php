<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Leysco100\LogisticsHub\Http\Controllers\Controller;
use Leysco100\Shared\Models\LogisticsHub\Models\RouteAssignment;
use Leysco100\Shared\Models\LogisticsHub\Models\RoutePlanning;
use Leysco100\Shared\Services\ApiResponseService;

class RouteAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request$request)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);

        $date = $request["date"];

        $SlpCode = $request["SlpCode"];

        $assignments = RouteAssignment::with('route', 'oslp')
            ->where("Date",$date)
            ->where( function ($q) use ($SlpCode){
                if ($SlpCode){
                    $q->where("SLPCode",$SlpCode);
                }
            })
            ->get();

        return (new ApiResponseService())->apiSuccessResponseService($assignments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'SlpCode' => 'required',
            'RouteID' => 'required',
        ]);

        DB::connection("tenant")->beginTransaction();
        try {

            $assignment =  RouteAssignment::create([
                'SlpCode' => $request['SlpCode'],
                'RouteID' => $request['RouteID'],
                'Date' => $request['date'],
                'Repeat' => $request['Repeat'],
            ]);

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($assignment);
        }catch (\Throwable $th){
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
