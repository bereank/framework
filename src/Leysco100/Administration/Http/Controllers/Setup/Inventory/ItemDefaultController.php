<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Inventory;


use Illuminate\Http\Request;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OIDG;
use Leysco100\Administration\Http\Controllers\Controller;

class ItemDefaultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OIDG::get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
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
        return OIDG::create([
            'Code' => $request['Code'], // Default Code
            'Name' => $request['Name'], // Default Name
            'CogsOcrCod' => $request['CogsOcrCod'],
            'CogsOcrCo2' => $request['CogsOcrCo2'],
            'CogsOcrCo3' => $request['CogsOcrCo3'],
            'CogsOcrCo4' => $request['CogsOcrCo4'],
            'CogsOcrCo5' => $request['CogsOcrCo5'],
        ]);
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
            $data = OIDG::where('id', $id)->first();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
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
            $details = [
                'Code' => $request['Code'], // Default Code
                'Name' => $request['Name'], // Default Name
                'CogsOcrCod' => $request['CogsOcrCod'],
                'CogsOcrCo2' => $request['CogsOcrCo2'],
                'CogsOcrCo3' => $request['CogsOcrCo3'],
                'CogsOcrCo4' => $request['CogsOcrCo4'],
                'CogsOcrCo5' => $request['CogsOcrCo5'],
            ];
            OIDG::where('id', $id)->update($details);
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
