<?php

namespace Leysco100\Inventory\Http\Controllers\API;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Inventory\Http\Controllers\Controller;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\EDG1;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OEDG;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSPP;

class DiscountGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ObjType =  request()->filled('ObjType') ? request()->input('ObjType') : 4;
     
        try {
            $data = OEDG::with('creator')
            ->when($ObjType, function ($query) use ($ObjType) {
                return $query->where('ObjType', $ObjType);
            })
            ->get();

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
        Log::info($request);
        $this->validate($request, [
            'Type' => 'required',
            'ValidFrom' => 'required',
            'ValidTo' => 'required',
            'ObjType' => 'required',
            'edg1' => 'required|array',
            'edg1.*.ObjKey' => 'required',
        ]);
        try {
            $obj = -1;
            if ($request['Type'] == 'C' || $request['Type'] == 'V') {
                $obj = 10;
            }
            if ($request['Type'] == 'S') {
                $obj = 2;
            }
            $data = OEDG::create([
                'Type' => $request['Type'],
                'ObjType' =>  $request['ObjType'],
                'DiscGrpTyp' => $obj  ?? null,
                'ObjCode' => $request['ObjCode'] ?? null,
                'DiscRel' => $request['DiscRel'] ?? 'L',
                'ValidFor' => $request['ValidFor'] ?? null,
                'ValidFrom' => $request['ValidFrom'] ?? null,
                'ValidTo' => $request['ValidTo'] ?? null,
                'UserSign' => Auth::user()->id,
            ]);
            foreach ($request['edg1'] as $item) {
                EDG1::create([
                    'DocEntry' => $data->id,
                    'ObjKey' => $item['ObjKey'] ?? null,
                    'ObjType' => $request['ObjType'] ?? null,
                    'DiscType' => $item['ForFree'] ? 'P' : 'D',
                    'Discount' => $item['Discount'] ?? 0,
                    'PayFor' => $item['PayFor'] ?? 1,
                    'ForFree' => $item['ForFree'] ?? 1,
                    'UpTo' => $item['UpTo'] ?? 1,
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
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
            $data = OEDG::where('id', $id)
                ->with('edg1:id,DocEntry,ObjKey as ItemCode,ObjType,DiscType,Discount,PayFor,ForFree,UpTo')
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
        $this->validate($request, [
            'ValidFrom' => 'required',
            'ValidTo' => 'required',
            'edg1' => 'required',
            'ObjType' => 'required'
        ]);
        try {
            OEDG::where('id', $id)->update([
                'Type' => $request['Type'],
                'ObjType' => $request['ObjType'] ?? null,
                'ObjCode' => $request['ObjCode'] ?? null,
                'DiscRel' => $request['DiscRel'] ?? null,
                'ValidFor' => $request['ValidFor'] ?? null,
                'ValidFrom' => $request['ValidFrom'] ?? null,
                'ValidTo' => $request['ValidTo'] ?? null,
                'UserSign2' => Auth::user()->id,
            ]);

            if ($request['edg1']) {
                EDG1::where('DocEntry', $id)->delete();
            }
            foreach ($request['edg1'] as $item) {

                EDG1::updateorCreate([
                    'DocEntry' => $id,
                    'ObjKey' => $item['ItemCode']
                ], [
                    'ObjType' => $request['ObjType'] ?? null,
                    'DiscType' => $item['ForFree'] ? 'P' : 'D',
                    'Discount' => $item['Discount'] ?? 0,
                    'PayFor' => $item['PayFor'] ?? 1,
                    'ForFree' => $item['ForFree'] ?? 1,
                    'UpTo' => $item['UpTo'] ?? 1,
                ]);
            }
            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfuly");
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $data = OEDG::where('id', $id)
                ->delete();
            $linesdata = EDG1::where('DocEntry', $id)
                ->delete();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
