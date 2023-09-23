<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Inventory;



use Illuminate\Http\Request;
use Leysco100\Shared\Models\ITB1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Finance\Models\ACP10;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITB;

class ItemGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OITB::with('itb1.glaccount')->get();
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
        $this->validate($request, [
            'ItmsGrpCod' => 'required',
        ]);
        DB::connection("tenant")->beginTransaction();
        try {
            $document_lines = is_array($request['document_lines']) ? 1 : 0;

            if ($document_lines != 1) {
                return (new ApiResponseService())->apiFailedResponseService("There are not accounts");
            }
            if (!$request['document_lines']) {
                return (new ApiResponseService())
                    ->apiFailedResponseService("Accounts array is empty");
            }

            $user = Auth::user();

            $newItemGroup = OITB::create([
                'ItmsGrpNam' => $request['ItmsGrpNam'],
                'ItmsGrpCod' => $request['ItmsGrpCod'],
                'UserSign' => $user->id,
                //Defaul Uom Group
                'IUoMEntry' => $request['IUoMEntry'],
            ]);

            //Saving Accounts

            foreach ($request['document_lines'] as $key => $value) {
                $oacp = ACP10::where('PrdCtgyCode', 1)
                    ->where('Field', $value['Field'])
                    ->first();

                $AcctCode = $oacp['$AcctCode'] ? $oacp['$AcctCode'] : null;

                $items = ITB1::create([
                    'ItmsGrpCod' => $newItemGroup->id,
                    'Field' => $value['Field'],
                    'Description' => $value['Description'],
                    'AcctCode' => $value['AcctCode'] ? $value['AcctCode'] : $AcctCode,
                    'Category' => $value['Category'],
                ]);
            }

            DB::connection("tenant")->commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }

        // $this->validate($request, [
        //     'ItmsGrpNam' => 'required',
        // ]);
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
            $data = OITB::where('id', $id)->first();
            $itb1 = ITB1::where('ItmsGrpCod', $id)->get();
            foreach ($itb1 as $key => $value) {
                $oact = ChartOfAccount::where('id', $value->AcctCode)->first();
                $value->Code = $oact ? $oact->AcctCode : null;
                $value->AcctName = $oact ? $oact->AcctName : null;
            }
            $data->itb1 = $itb1;
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
            'ItmsGrpNam' => 'required',
        ]);

        DB::connection("tenant")->beginTransaction();
        try {
            $details = [
                'ItmsGrpNam' => $request['ItmsGrpNam'],
            ];
            OITB::where('id', $id)->update($details);

            foreach ($request['itb1'] as $key => $value) {
                $listdetals = [
                    'AcctCode' => $value['AcctCode'],
                    'AcctName' => $value['AcctName'],
                ];

                ITB1::where('id', $value['id'])->update($listdetals);
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
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
