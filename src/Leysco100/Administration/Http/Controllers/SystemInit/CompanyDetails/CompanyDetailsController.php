<?php

namespace App\Http\Controllers\API\Administration\Setup\SystemInit\CompanyDetails;

use App\Domains\Administration\Models\OADM;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = OADM::where('id', 1)->first();
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
        return OADM::create([
            'MainCurncy' => $request['MainCurncy'], //Main Currency
            'DfActCurr' => $request['DfActCurr'], // Default Currency
            'SysCurrncy' => $request['SysCurrncy'], // System Currency
            'InvntSystm' => $request['InvntSystm'], // Perpetual Inventory System
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
        //
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
            $company = OADM::where('id', 1)->first();
            if ($company) {
                $generalSettings = [
                    'MainCurncy' => $request['MainCurncy'], //Main Currency
                    'DfActCurr' => $request['DfActCurr'], // Default Currency
                    'SysCurrncy' => $request['SysCurrncy'], // System Currency
                    'InvntSystm' => $request['InvntSystm'], // Item Group Valuation Method

                ];

                OADM::where('id', 1)->update(array_filter($generalSettings));
            } else {
                OADM::create([
                    'MainCurncy' => $request['MainCurncy'], //Main Currency
                    'DfActCurr' => $request['DfActCurr'], // Default Currency
                    'SysCurrncy' => $request['SysCurrncy'], // System Currency
                    'InvntSystm' => $request['InvntSystm'], // Item Group Valuation Method
                ]);
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
    public function storeCompanyDetails(Request $request)
    {
        try {
            $company = OADM::where('id', 1)->first();
            if ($company) {
                $generalSettings = [
                    'CompnyName' => $request['CompnyName'],
                    'BTWStreet' => $request['BTWStreet'],
                    'DRBlock2' => $request['DRBlock2'],
                    'E_Mail' => $request['E_Mail'],
                    'HQLocation' => $request['HQLocation'],
                    'BTWCity' => $request['BTWCity'],
                    'BTWZip' => $request['BTWZip'],
                    'E_Mail' => $request['E_Mail'],
                    'Phone1' => $request['Phone1'],

                ];

                OADM::where('id', 1)->update(array_filter($generalSettings));
            } else {
                OADM::create([
                    'CompnyName' => $request['CompnyName'],
                    'BTWStreet' => $request['BTWStreet'],
                    'DRBlock2' => $request['DRBlock2'],
                    'E_Mail' => $request['E_Mail'],
                    'HQLocation' => $request['HQLocation'],
                    'BTWCity' => $request['BTWCity'],
                    'BTWZip' => $request['BTWZip'],
                    'E_Mail' => $request['E_Mail'],
                    'Phone1' => $request['Phone1'],
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
