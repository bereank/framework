<?php

namespace Leysco100\Payments\Http\Controllers\API\PaymentsIntegrations;



use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;

class AriPaymentsController extends Controller
{
    public function PromptStkPush(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_no' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'payingshortcode' => ['required'],
            'reference' => ['required'],
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }

        $baseUrl = url('/');
        $partyURL =  'https://cargen.pit.co.ke/api/v1/pit/posstkout';

        try {
            $reqData = array(
                "phone_no" => $request->phone_no,
                "amount" => $request->amount,
                "payingshortcode" => $request->payingshortcode,
                "callback_url" => $baseUrl . "/payments/incoming/third-party/ari/mpesa-callback",
                "reference" => $request->reference,
            );

            $res =  $this->dataHydration($partyURL, $reqData);
            if ($res['status_code'] == 200) {
                return (new ApiResponseService())->apiSuccessResponseService($res['response']);
            } else {
                return (new ApiResponseService())->apiFailedResponseService("Failed To initiate Stk push");
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function transQuery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TransID' => ['required'],
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }
        $partyURL =  "https://cargen.pit.co.ke/api/v1/pit/getpospayresp";

        $reqData = array(
            "TransID" => $request->TransID
        );

        try {
            $res =  $this->dataHydration($partyURL, $reqData);
            if ($res['status_code'] == 200) {
                $res = [
                    "record" =>  $res['response']->payload[0],
                    "balance" => $res['response']->balance,
                    "message" => $res['response']->message
                ];
                return (new ApiResponseService())->apiSuccessResponseService($res);
            } else {
                return (new ApiResponseService())->apiFailedResponseService("Failed To Fetch Transaction");
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function mpesa_callback(Request $request)
    {
        $user = User::where('id', 1)->first();
        Auth::login($user);

        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId(218);

        $data = [
            'MSISDN' => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][3],
            'TransAmount' => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][0]["Value"] ?? 0,
            'TransTime' =>  now(),
            'TransID' => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"],
            'Balance' => $request["data"]["Body"]["stkCallback"]["CallbackMetadata"]["Item"][0]["Value"] ?? 0,
            'DocNum' =>  $Numbering['NextNumber'],
            'BusinessKey' => $request["reference"],
            'Source' => 1,
            'ObjType' => 218,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),

        ];

        $callback = DB::connection("tenant")->table("o_c_r_p_s")->insert($data);


        if (!$callback) {
            return (new ApiResponseService())->apiFailedResponseService("Process failed, Server Error", $request->all());
        }
        return (new ApiResponseService())->apiSuccessResponseService($callback);
    }


    public function getTransData(Request $request)
    {
        try {
            $callback = DB::connection("tenant")->table("mpesa_callback")->select("transactionAmount", "transactionMpesaRef")->where("transactionRefNumber", $request->ref)->first();
            if (!$callback) {
                return (new ApiResponseService())->apiFailedResponseService("Process failed, Server Error", $request->all());
            }
            return (new ApiResponseService())->apiSuccessResponseService($callback);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function dataHydration($partyURL, $reqData)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $partyURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($reqData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'status_code' => $httpCode,
            'response' => json_decode($response),
        ];
    }
}
