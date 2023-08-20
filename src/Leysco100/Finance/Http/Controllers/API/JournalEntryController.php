<?php

namespace App\Http\Controllers\API\Financials;

use App\APDI;
use App\ChartOfAccount;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Domains\Finance\Models\JDT1;
use App\Domains\Finance\Models\OJDT;
use App\Http\Controllers\Controller;
use App\Domains\Shared\Services\ApiResponseService;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OJDT::with('jdt1.oact')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $newItem = OJDT::create([
                'Series' => $request['Series'],
                'Memo' => $request['Memo'],
                'RefDate' => $request['RefDate'],
                'DueDate' => $request['DueDate'],
                'TaxDate' => $request['TaxDate'],
                'LocTotal' => $request['LocTotal'],
            ]);

            foreach ($request['document_lines'] as $key => $value) {
                $firstAccount = ChartOfAccount::where('id', $value['Account'])->first();

                if (!$firstAccount) {
                    return (new ApiResponseService())->apiFailedResponseService("GL Account Doest not exist");
                }
                $newItem = JDT1::create([
                    'TransId' => $newItem->id,
                    'Line_ID' => $key + 1,
                    'Account' => $value['Account'],
                    'Debit' => $value['Debit'],
                    'Credit' => $value['Credit'],
                    'DueDate' => $value['DueDate'],
                    'TaxDate' => $value['TaxDate'],
                ]);

                $firstAccountDetails = [
                    'CurrTotal' => $firstAccount->CurrTotal + $value['Debit'] + $value['Credit'],
                ];
                $firstAccount->update($firstAccountDetails);
            }

            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService();
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
            $data = OJDT::with('jdt1.oact')->where('id', $id)
                ->first();
            if (!$data) {
                return (new ApiResponseService())->apiFailedResponseService("Document does not exist");
            }
            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', $data->TransType)
                ->first();

            $data->document = $DocumentTables->ObjectHeaderTable::with(
                'outlet',
                'objecttype',
                'document_lines.oitm.itm1',
                'document_lines.oitm.inventoryuom',
                'document_lines.oitm.ougp.ouom',
                'document_lines.oitm.oitb'
            )
                ->where('id', $data->BaseRef)
                ->first();

            return (new ApiResponseService())->apiSuccessResponseService($data);
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
}
