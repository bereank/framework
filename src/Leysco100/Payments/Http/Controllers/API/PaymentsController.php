<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Payments\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Services\DocumentsService;

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
            $data = OCRP::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function paymentNotification(Request $request)
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
                    $payment['BusinessKey'] = $paymentData['businessKey'] ?? "";
                    $payment['BusinessKeyType'] = $paymentData['businessKeyType'] ?? "";
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

        $originatorConversationID = $data['header']['originatorConversationID'] ?? '';
        $messageID = $data['header']['messageID'] ?? '';

        try {
            $transaction =   OCRP::create($payment);

            $data = [
                'header' => [
                    'messageID' => $messageID,
                    'originatorConversationID' => $originatorConversationID,
                    'statusCode' => '0',
                    'statusMessage' => 'Notification received',
                ],
                'responsePayload' => [
                    'transactionInfo' => [
                        'transactionId' => $transaction->id,
                    ],
                ],
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            $data = [
                'header' => [
                    'messageID' => $messageID,
                    'originatorConversationID' => $originatorConversationID,
                    'statusCode' => '1',
                    'statusMessage' => $th->getMessage(),
                ],
                'responsePayload' => [
                    'transactionInfo' => [
                        'transactionId' => $transaction->id,
                    ],
                ],
            ];
            Log::info($th);
            return response()->json($data);
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
            return response()->json($data);
        }
    }

    public function paymentQuery(Request $request)
    {
        $user = User::where('id', 1)->first();
        Auth::login($user);

        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId(218);

        $data =  $request->all();


        $messageID = $data['header']['messageID'] ?? '';

        try {
            $responseData = [
                "header" => [
                    "messageID" => $messageID,
                    "originatorConversationID" => "",
                    "statusCode" => "0",
                    "statusMessage" => "Processed Successfully",
                ],
                "responsePayload" => [
                    "transactionInfo" => [
                        "transactionId" => $Numbering['NextNumber'],
                        "utilityName" => "Cash Customer",
                        "customerName" => "",
                        "amount" => "",
                        "currency" => "KES",
                        "billType" => "FIXED",
                        "billDueDate" => "",
                    ],
                ],
            ];
            (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
            return response()->json($responseData);
        } catch (\Throwable $th) {
            $responseData = [
                "header" => [
                    "messageID" => $messageID,
                    "originatorConversationID" => "",
                    "statusCode" => "1",
                    "statusMessage" => $th->getMessage(),
                ],
                "responsePayload" => [
                    "transactionInfo" => [
                        "transactionId" => $Numbering['NextNumber'],
                        "utilityName" => "Cash Customer",
                        "customerName" => "",
                        "amount" => "",
                        "currency" => "KES",
                        "billType" => "FIXED",
                        "billDueDate" => "",
                    ],
                ],
            ];
            (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
            Log::info($th);
            return response()->json($responseData);
        }
    }
}
