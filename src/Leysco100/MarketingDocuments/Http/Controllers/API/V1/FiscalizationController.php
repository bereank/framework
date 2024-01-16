<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\MarketingDocuments\Models\FSC1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OFSC;
use Leysco100\Shared\Services\ApiResponseService;

class FiscalizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            DB::connection("tenant")->beginTransaction();
            $data = OFSC::all();
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        }
        catch (\Throwable $th){
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
        try {
            DB::connection("tenant")->beginTransaction();

            $invoice_ids = $request["InvoiceId"];

            $ofsc = new OFSC();

            if (!is_array($invoice_ids)){
                $ofsc->InvoiceId = $request->InvoiceId;
                $ofsc->save();
            }else{
                $data = [];
                foreach ($invoice_ids as $invoice_id){
                    $data[] = [
                        "created_at"=>date("Y-m-d H:i:s"),
                        "updated_at"=>date("Y-m-d H:i:s"),
                        "InvoiceId"=>$invoice_id
                    ];
                }
                $ofsc->insert($data);
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService($ofsc);
        }catch (\Throwable $th){

            DB::connection("tenant")->rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th);
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
            $ofsc = OFSC::where("InvoiceId",$id)->first();
            if (!$ofsc){
                return (new ApiResponseService())->apiNotFoundResponse("data not found");
            }
            //step one: create new record on fsc1 table
            $fsc1 = new FSC1();
            $fsc1->cache = json_encode($request->all());
            $fsc1->U_ControlCode = $request["U_ControlCode"];
            $fsc1->U_RelatedInv = $request["U_RelatedInv"];
            $fsc1->U_CUInvoiceNum = $request["U_CUInvoiceNum"];
            $fsc1->U_QRCode = $request["U_QRCode"];
            $fsc1->U_QrLocation = $request["U_QrLocation"];
            $fsc1->U_ReceiptNo = $request["U_ReceiptNo"];
            $fsc1->U_CommitedTime = $request["U_CommitedTime"];
            $fsc1->InvoiceId = $id;
            $fsc1->message = $request["message"];
            $fsc1->statusCode = $request["statusCode"];
            $fsc1->save();
            //step two: delete existing ofsc record
            $ofsc->delete();
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService();
        }catch (\Throwable $th){
            DB::connection("tenant")->rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th);
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
