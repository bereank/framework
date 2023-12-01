<?php

namespace Leysco100\Inventory\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBFC;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBSL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OIBQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;


class BinLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $WhsCode =  request()->filled('WhsCode') ? request()->input('WhsCode') : false;

            $data = OBIN::with('warehouse')
                ->when($WhsCode, function ($query) use ($WhsCode) {
                    return $query->where('WhsCode', $WhsCode);
                })
                ->get();
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

        // Validate the incoming request data
        $validatedData = $request->validate([
            'AbsEntry' => 'nullable',
            'WhsCode' => 'required',
        ]);
        $data = OWHS::where('WhsCode', $request['WhsCode'])->first();
        $BinCode = '';
        if ($data) {
            $BinCode = $data->WhsCode;

            if ($request['SL1Code']) {
                $SL1Abs = OBSL::where('SLCode', $request['SL1Code'])->first();
                $BinCode .= $data->BinSeptor . $request['SL1Code'];
            }

            if ($request['SL2Code']) {
                $BinCode .= $data->BinSeptor . $request['SL2Code'];
                $SL2Abs = OBSL::where('SLCode', $request['SL2Code'])->first();
            }

            if ($request['SL3Code']) {
                $BinCode .= $data->BinSeptor . $request['SL3Code'];
                $SL3Abs = OBSL::where('SLCode', $request['SL3Code'])->first();
            }

            if ($request['SL4Code']) {
                $BinCode .= $data->BinSeptor . $request['SL4Code'];
                $SL4Abs = OBSL::where('SLCode', $request['SL4Code'])->first();
            }
        }

        try {
            $obin = new OBIN([
                'SysBin' => 0,
                'BinCode' =>  $BinCode,
                'WhsCode' => $data->WhsCode,
                'SL1Abs' => $SL1Abs->id ?? null,
                'SL1Code' => $request['SL1Code'] ?? null,
                'SL2Abs' => $SL2Abs->id ?? null,
                'SL2Code' => $request['SL2Code'] ?? null,
                'SL3Abs' => $SL3Abs->id ?? null,
                'SL3Code' => $request['SL3Code'] ?? null,
                'SL4Abs' => $SL4Abs->id ?? null,
                'SL4Code' => $request['SL4Code'] ?? null,
                'Attr1Abs' => $request['Attr1Abs'] ?? null,
                'Attr1Val' => $request['Attr1Val'] ?? null,
                'Attr2Abs' => $request['Attr2Abs'] ?? null,
                'Attr2Val' => $request['Attr2Val'] ?? null,
                'Attr3Abs' => $request['Attr3Abs'] ?? null,
                'Attr3Val' => $request['Attr3Val'] ?? null,
                'Attr4Abs' => $request['Attr4Abs'] ?? null,
                'Attr4Val' => $request['Attr4Val'] ?? null,
                'Attr5Abs' => $request['Attr5Abs'] ?? null,
                'Attr5Val' => $request['Attr5Val'] ?? null,
                'Attr6Abs' => $request['Attr6Abs'] ?? null,
                'Attr6Val' => $request['Attr6Val'] ?? null,
                'Attr7Abs' => $request['Attr7Abs'] ?? null,
                'Attr7Val' => $request['Attr7Val'] ?? null,
                'Attr8Abs' => $request['Attr8Abs'] ?? null,
                'Attr8Val' => $request['Attr8Val'] ?? null,
                'Attr9Abs' => $request['Attr9Abs'] ?? null,
                'Attr9Val' => $request['Attr9Val'] ?? null,
                'Attr10Abs' => $request['Attr10Abs'] ?? null,
                'Attr10Val' => $request['Attr10Val'] ?? null,
                'Disabled' => $request['Disabled'] ?? 0,
                'Descr' => $request['Descr'] ?? null,
                'BarCode' => $request['BarCode'] ?? null,
                'AltSortCod' => $request['AltSortCod'] ?? null,
                'ItmRtrictT' => $request['ItmRtrictT'] ?? null,
                'SpcItmCode' => $request['SpcItmCode'] ?? null,
                'SpcItmGrpC' => $request['SpcItmGrpC'] ?? null,
                'SngBatch' => $request['SngBatch'] ?? null,
                'RtrictType' => $request['RtrictType'] ?? null,
                'RtrictResn' => $request['RtrictResn'] ?? null,
                'UserSign' => Auth::user()->id,
                'MinLevel' => $request['MinLevel'] ?? null,
                'MaxLevel' => $request['MaxLevel'] ?? null,
                'ReceiveBin' => $request['ReceiveBin'] ?? 0,
                'NoAutoAllc' =>  $request['NoAutoAllc'] ?? null,
                'MaxWeight1' =>  $request['MaxWeight1'] ?? null,
                'Wght1Unit' =>  $request['Wght1Unit'] ?? null,
                'MaxWeight2' =>  $request['MaxWeight2'] ?? null,
                'Wght2Unit' =>  $request['Wght2Unit'] ?? null,
                'UoMRtrict' =>  $request['UoMRtrict'] ?? null,
                'SpcUoMCode' =>  $request['SpcUoMCode'] ?? null,
                'SpcUGPCode' =>  $request['SpcUGPCode'] ?? null,
                'SngUoMCode' =>  $request['SngUoMCode'] ?? null,
            ]);
            $obin->save();
            return (new ApiResponseService())
                ->apiSuccessResponseService($obin);
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
            $data = OBIN::find($id);
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'AbsEntry' => 'nullable',
            'WhsCode' => 'required',
        ]);
        $data = OWHS::where('WhsCode', $request['WhsCode'])->first();
        $BinCode = '';
        if ($data) {
            $BinCode = $data->WhsCode;

            if ($request['SL1Code']) {
                $BinCode .= $data->BinSeptor . $request['SL1Code'];
            }

            if ($request['SL2Code']) {
                $BinCode .= $data->BinSeptor . $request['SL2Code'];
            }

            if ($request['SL3Code']) {
                $BinCode .= $data->BinSeptor . $request['SL3Code'];
            }

            if ($request['SL4Code']) {
                $BinCode .= $data->BinSeptor . $request['SL4Code'];
            }
        }

        try {
            $obin = OBIN::where('id', $id)->update([
                'SysBin' => 0,
                'BinCode' =>  $BinCode,
                'WhsCode' => $data->WhsCode,
                'SL1Abs' => $request['SL1Abs'] ?? null,
                'SL1Code' => $request['SL1Code'] ?? null,
                'SL2Abs' => $request['SL2Abs'] ?? null,
                'SL2Code' => $request['SL2Code'] ?? null,
                'SL3Abs' => $request['SL3Abs'] ?? null,
                'SL3Code' => $request['SL3Code'] ?? null,
                'SL4Abs' => $request['SL4Abs'] ?? null,
                'SL4Code' => $request['SL4Code'] ?? null,
                'Attr1Abs' => $request['Attr1Abs'] ?? null,
                'Attr1Val' => $request['Attr1Val'] ?? null,
                'Attr2Abs' => $request['Attr2Abs'] ?? null,
                'Attr2Val' => $request['Attr2Val'] ?? null,
                'Attr3Abs' => $request['Attr3Abs'] ?? null,
                'Attr3Val' => $request['Attr3Val'] ?? null,
                'Attr4Abs' => $request['Attr4Abs'] ?? null,
                'Attr4Val' => $request['Attr4Val'] ?? null,
                'Attr5Abs' => $request['Attr5Abs'] ?? null,
                'Attr5Val' => $request['Attr5Val'] ?? null,
                'Attr6Abs' => $request['Attr6Abs'] ?? null,
                'Attr6Val' => $request['Attr6Val'] ?? null,
                'Attr7Abs' => $request['Attr7Abs'] ?? null,
                'Attr7Val' => $request['Attr7Val'] ?? null,
                'Attr8Abs' => $request['Attr8Abs'] ?? null,
                'Attr8Val' => $request['Attr8Val'] ?? null,
                'Attr9Abs' => $request['Attr9Abs'] ?? null,
                'Attr9Val' => $request['Attr9Val'] ?? null,
                'Attr10Abs' => $request['Attr10Abs'] ?? null,
                'Attr10Val' => $request['Attr10Val'] ?? null,
                'Disabled' => $request['Disabled'] ?? 0,
                'Descr' => $request['Descr'] ?? null,
                'BarCode' => $request['BarCode'] ?? null,
                'AltSortCod' => $request['AltSortCod'] ?? null,
                'ItmRtrictT' => $request['ItmRtrictT'] ?? null,
                'SpcItmCode' => $request['SpcItmCode'] ?? null,
                'SpcItmGrpC' => $request['SpcItmGrpC'] ?? null,
                'SngBatch' => $request['SngBatch'] ?? null,
                'RtrictType' => $request['RtrictType'] ?? null,
                'RtrictResn' => $request['RtrictResn'] ?? null,
                'UserSign' => Auth::user()->id,
                'MinLevel' => $request['MinLevel'] ?? null,
                'MaxLevel' => $request['MaxLevel'] ?? null,
                'ReceiveBin' => $request['ReceiveBin'] ?? 0,
                'NoAutoAllc' =>  $request['NoAutoAllc'] ?? null,
                'MaxWeight1' =>  $request['MaxWeight1'] ?? null,
                'Wght1Unit' =>  $request['Wght1Unit'] ?? null,
                'MaxWeight2' =>  $request['MaxWeight2'] ?? null,
                'Wght2Unit' =>  $request['Wght2Unit'] ?? null,
                'UoMRtrict' =>  $request['UoMRtrict'] ?? null,
                'SpcUoMCode' =>  $request['SpcUoMCode'] ?? null,
                'SpcUGPCode' =>  $request['SpcUGPCode'] ?? null,
                'SngUoMCode' =>  $request['SngUoMCode'] ?? null,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($obin);
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
    }

    public function BinLocationItems(Request $request, $id)
    {
        try {
            $oibq = OIBQ::where('BinAbs', $id)
                ->with('bin_location')
                ->with('item:id,ItemCode,ItemName')
                ->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($oibq);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
