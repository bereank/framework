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
    public function index(Request $request)
    {
        $object_type = $request->query('object_type',  13);

        try {
            $data = FSC2::where('ObjectCode', $object_type)
                ->select(
                    "id",
                    "BaseDocEntry",
                    "DocEntry",
                    "ObjType",
                    "ObjectCode"
                )
                ->get()->toArray();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Exception $e) {
            return (new ApiResponseService())->apiFailedResponseService($e->getMessage());
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
        $fiscal = FSC2::where('BaseDocEntry', $request['DocEntry'])->where('ObjectCode', $request['ObjType'])->first();
        if ($fiscal) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("Document Fiscalizing.....");
        }
        $fiscal_success = OFSC::where('BaseDocEntry', $request['DocEntry'])->where('ObjectCode', $request['ObjType'])
            ->where('Status', 'Y')->first();
        if ($fiscal_success) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("Document already Fiscalized Successfully");
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
                $ofsc->BaseDocEntry = $request->DocEntry;
                $ofsc->ObjectCode = $request['ObjType'];
                $ofsc->UserSign = Auth::user()->id;
                $ofsc->OwnerCode = Auth::user()->EmpID;
                $ofsc->save();
                FSC2::create([
                    "DocEntry" => $ofsc->id,
                    'ObjectCode' => $request['ObjType'],
                    'BaseDocEntry' => $request->DocEntry,
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
                        'ObjectCode' =>  $request['ObjType'],
                        'BaseDocEntry' => $doc_id,
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
            $ofsc = OFSC::where('id',  $id)->first();

            if (!$ofsc) {

                return (new ApiResponseService())->apiFailedResponseService("Data not found");
            }
            //step one: create new record on fsc1 table
            $fsc1 = FSC1::create(
                [
                    'DocEntry' =>  $id,
                    'cache' => json_encode($request->all()),
                    'RelatedInvNum' => $request["RelatedInvNum"] ?? null,
                    'BaseInvNum' => $request["BaseInvNum"] ?? null,
                    'ControlCode' => $request["ControlCode"] ?? null,
                    'RAuthorityURL' => $request["RAuthorityURL"] ?? null,
                    'CUInvNum' => $request["CUInvNum"] ?? null,
                    'ReceiptNo' => $request["ReceiptNo"] ?? null,
                    'message' => $request["message"] ?? null,
                    'Status' => $request["statusCode"] ?? null,
                ]
            );
            //step two: delete existing fsc2 record
            FSC2::where('DocEntry',  $id)->delete();
            OFSC::where('id',  $id)->update([
                'Status' => 1
            ]);
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
