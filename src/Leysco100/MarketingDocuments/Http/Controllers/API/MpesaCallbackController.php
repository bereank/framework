<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;

class MpesaCallbackController extends Controller
{
    public function mpesa_callback(Request $request)
    {
        $data = [
            "transactionData" => json_encode($request->all()),
            "transactionAmount" => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][0]["Value"],
            "transactionMpesaRef" => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"],
            "transactionPhoneNumber" => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][3]["Value"],
            "transactionRefNumber" => $request["reference"],

            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),

        ];

        $callback = DB::connection("tenant")->table("mpesa_callback")->insert($data);

        if (!$callback) {
            return (new ApiResponseService())->apiFailedResponseService("Process failed, Server Error", $request->all());
        }
        return (new ApiResponseService())->apiSuccessResponseService($callback);
    }

    public function getTransData(Request $request)
    {
        try{
            $callback = DB::connection("tenant")->table("mpesa_callback")->select("transactionAmount","transactionMpesaRef")->where("transactionRefNumber", $request->ref)->first();
            if (!$callback) {
                return (new ApiResponseService())->apiFailedResponseService("Process failed, Server Error", $request->all());
            }
        return (new ApiResponseService())->apiSuccessResponseService($callback);
        }catch (\Throwable $th){
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
