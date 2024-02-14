<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\FSC1;
use Leysco100\Shared\Models\MarketingDocuments\Models\FSC2;
use Leysco100\Shared\Models\MarketingDocuments\Models\OFSC;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;

class FiscalizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            DB::connection("tenant")->beginTransaction();
            $data = FSC2::all()->toArray();
            //            $data = OFSC::select("InvoiceId")->get()->toArray();

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th);
        }
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
        $validator = Validator::make($request->all(), [
            'ObjType' => 'required',
            'DocEntry' => 'required'
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }

        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $request['ObjType'])
            ->first();

        if (!$DocumentTables) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("Object Code doesn't exist");
        }
        $doc_ids = $request["DocEntry"];
        if (!is_array($doc_ids)) {
            $data = $DocumentTables->ObjectHeaderTable::with('document_lines:id,DocEntry')
                ->where('id', $doc_ids)
                ->first();

            if (!$data) {
                return (new ApiResponseService())->apiSuccessAbortProcessResponse("Document Does't Exist !!! " . $doc_ids ?? "");
            }
        } else {
            foreach ($doc_ids as $invoice_id) {
                $data = $DocumentTables->ObjectHeaderTable::with('document_lines:id,DocEntry')
                    ->where('id', $invoice_id)
                    ->first();

                if (!$data) {
                    return (new ApiResponseService())->apiSuccessAbortProcessResponse("Document Does't Exist !!! " . $invoice_id ?? "");
                }
            }
        }
        //check if document is closed
        if ($data->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("The Document is Closed");
        }


        try {
            DB::connection("tenant")->beginTransaction();
            $ofsc = new OFSC();

            if (!is_array($doc_ids)) {
                $ofsc->DocEntry = $request->DocEntry;
                $ofsc->ObjCode = $request['ObjType'];
                $ofsc->UserSign = Auth::user()->id;
                $ofsc->OwnerCode = Auth::user()->EmpID;
                $ofsc->save();
                FSC2::create([
                    "DocEntry" => $ofsc->id,
                    'ObjCode' => $request['ObjType'],
                    'DocId' => $request->DocEntry,
                ]);
            } else {
                $data = [];
                foreach ($doc_ids as $doc_id) {

                    $ofsc =   $ofsc->create([
                        'DocEntry' => $doc_id,
                        'ObjCode' =>  $request['ObjType'],
                        'UserSign' => Auth::user()->id,
                        'OwnerCode' => Auth::user()->EmpID,
                    ]);
                    FSC2::create([
                        "DocEntry" => $ofsc->id,
                        'ObjCode' =>  $request['ObjType'],
                        'DocId' => $doc_id,
                    ]);
                }
            }

            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($ofsc);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

        try {
            DB::connection("tenant")->beginTransaction();
            $ofsc = OFSC::where('id', $id)->first();

            if (!$ofsc) {

                return (new ApiResponseService())->apiFailedResponseService("Data not found");
            }
            //step one: create new record on fsc1 table
            $fsc1 = FSC1::firstOrCreate(
                [
                    'DocId' => $id,
                    'ObjCode' => $request["ObjCode"],
                    'DocEntry' => $request["DocEntry"]
                ],
                [
                    'cache' => json_encode($request->all()),
                    'U_ControlCode' => $request["U_ControlCode"],
                    'U_RelatedInv' => $request["U_RelatedInv"],
                    'U_CUInvoiceNum' => $request["U_CUInvoiceNum"],
                    'U_QRCode' => $request["U_QRCode"],
                    'U_QrLocation' => $request["U_QrLocation"],
                    'U_ReceiptNo' => $request["U_ReceiptNo"],
                    'U_CommitedTime' => $request["U_CommitedTime"],
                    'message' => $request["message"],
                    'statusCode' => $request["statusCode"],
                ]
            );

            //step two: delete existing fsc2 record
            FSC2::where('DocEntry', $request["DocEntry"])->delete();
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($fsc1->message);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
