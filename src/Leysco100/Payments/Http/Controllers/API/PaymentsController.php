<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Payments\Http\Controllers\Controller;

class PaymentsController extends Controller
{

    public function index()
    {

        try {
            $data = OCRP::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show($id)
    {

        try {

            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function store(Request $request)
    {
        // $publicKey = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'public_key.pem');
        // if (!$publicKey) {
        //     die("Unable to load public key");
        // }
        // $message = $request->getContent();
       
        // $signature = hex2bin(bin2hex($request->header('signature')));

        // $result = openssl_verify($message, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        // if ($result === 1) {
        //     return "Signature is valid. Message has not been tampered with.";
        // } elseif ($result === 0) {
        //     return "Signature is invalid. Message and signature do not match.";
        // } else {
        //     return "An error occurred during signature verification.";
        // }

        $data =  $request->all();

        if (array_key_exists('requestPayload', $data)) {
            if (array_key_exists("additionalData", $data['requestPayload'])) {
                if (array_key_exists("notificationData", $data['requestPayload']['additionalData'])) {
                    $paymentData =   $data['requestPayload']['additionalData']['notificationData'];
                    $payment = [];
                    $payment['ShortCode'] = $paymentData['businessKey'] ?? "";
                    // $payment[''] = $paymentData['businessKeyType'] ?? "";
                    $payment['MSISDN'] = $paymentData['debitMSISDN'] ?? "";
                    $payment['TransactAmount'] = $paymentData['transactionAmt'] ?? "";
                    $payment['TransactDate'] = $paymentData['transactionDate'] ?? "";
                    $payment['TransactID'] = $paymentData['transactionID'] ?? "";
                    $payment['FirstName'] = $paymentData['firstName'] ?? "";
                    $payment['MiddleName'] = $paymentData['middleName'] ?? "";
                    $payment['LastName'] = $paymentData['lastName'] ?? "";
                    $payment['Currency'] = $paymentData['currency'] ?? "";
                    $payment['Dscription'] = $paymentData['narration'] ?? "";
                    $payment['TransactType'] = $paymentData['transactionType'] ?? "";
                    $payment['Balance'] = $paymentData['balance'] ?? "";
                    $payment['ObjType'] = 217;
                }
            }
        }

        try {
            $p =   OCRP::create($payment);
            return (new ApiResponseService())->apiSuccessResponseService($p);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $data = OCRP::where('id', $id)->update([
                'Balance' => $request['balance'] ?? "",
                'ExtRef' => $request['ExtRef'] ?? "",
                'ExtDocTotal' => $request['ExtDocTotal'] ?? "",
                'ExtRefDocNum' => $request['ExtRefDocNum'] ?? "",
            ]);
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
