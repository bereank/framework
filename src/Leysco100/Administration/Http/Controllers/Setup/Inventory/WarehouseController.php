<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;
use Leysco100\Shared\Services\ApiResponseService;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $BPLId = \Request::get('branchID');
            $data = OWHS::where(function ($q) use ($BPLId) {
                if ($BPLId) {
                    $q->where('BPLId', $BPLId)
                        ->orWhereIn('WhsCode', ['L001', 'IMPGIT']);
                }
            })->get();

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
        DB::beginTransaction();
        try {
            $document_lines = is_array($request['document_lines']) ? 1 : 0;

            if ($document_lines != 1) {
                return (new ApiResponseService())->apiFailedResponseService("There are not accounts");
            }
            if (!$request['document_lines']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Accounts array is empty");
            }
            $newWaharehouse = OWHS::create([
                'WhsCode' => $request['WhsCode'],
                'WhsName' => $request['WhsName'],
            ]);

            foreach ($request['document_lines'] as $key => $value) {
                $oacp = ACP10::where('PrdCtgyCode', 1)
                    ->where('Field', $value['Field'])
                    ->first();

                $AcctCode = $oacp['$AcctCode'] ? $oacp['$AcctCode'] : null;

                $items = WHS1::create([
                    'WhsCode' => $newWaharehouse->id,
                    'Field' => $value['Field'],
                    'Description' => $value['Description'],
                    'AcctCode' => $value['AcctCode'] ? $value['AcctCode'] : $AcctCode,
                    'Category' => $value['Category'],
                ]);
            }

            DB::commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::rollback();
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
            $ItemCode = \Request::get('ItemCode');

            $data = OWHS::where('id', $id)->first();

            if ($ItemCode) {
                $data->OnHand = OITW::where('WhsCode', $data->WhsCode)->where('ItemCode', $ItemCode)->value('OnHand');
            }
            $whs1 = WHS1::where('WhsCode', $id)->get();
            foreach ($whs1 as $key => $value) {
                $oact = ChartOfAccount::where('id', $value->AcctCode)->first();
                $value->Code = $oact ? $oact->AcctCode : null;
                $value->AcctName = $oact ? $oact->AcctName : null;
            }
            $data->whs1 = $whs1;
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
        $this->validate($request, [
            'WhsName' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $details = [
                'WhsName' => $request['WhsName'],
            ];
            OWHS::where('id', $id)->update($details);

            foreach ($request['whs1'] as $key => $value) {
                $listdetals = [
                    'AcctCode' => $value['AcctCode'],
                    'AcctName' => $value['AcctName'],
                ];

                WHS1::where('id', $value['id'])->update($listdetals);
            }
            DB::commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::rollback();
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
        $data = Warehouse::findOrFail($id);
        $data->delete();
    }
}
