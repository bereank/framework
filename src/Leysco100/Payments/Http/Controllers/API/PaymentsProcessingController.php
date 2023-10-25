<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Payments\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Services\DocumentsService;

class PaymentsProcessingController extends Controller
{
    public function kcbPaymentNotification(Request $request)
    {
        $user = User::where('id', 1)->first();
        Auth::login($user);

        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId(218);
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
                    $payment['DocNum'] =  $Numbering['NextNumber'];
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
            (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
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
    public function kcbPaymentQuery(Request $request)
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

    public function  eqbValidation(Request $request)
    {

        if (Auth::attempt([
            'email' => $request->username,
            'password' => $request->password
        ])) {
            $data = [
                "amount" => "230.0",
                "billName" => "Test_Account_3",
                "billNumber" => "33333333",
                "billerCode" => "123456",
                "createdOn" => "2016-12-20",
                "currencyCode" => "KES",
                "customerName" => "Test_Account_3",
                "customerRefNumber" => "33333333",
                "description" => "subscription fees",
                "dueDate" => "2016-12-21",
                "expiryDate" => "2016-12-29",
                "Remarks" => "Fees",
                "type" => "1"
            ];
            $invalidRes =     [
                "amount" => 0,
                "billNumber" => "null",
                "billName" => "null",
                "description" => "bill number not found",
                "type" => "1"
            ];
            return $data;
        } else {
            return response()->json('Auth Failed');
        }
    }

    public function eqbPaymentNotification(Request $request)
    {

        // "username": "Equity",
        // "password": "3pn!Ty@zoi9",
        // "billNumber": "123456",
        // "billAmount": "100",
        // "CustomerRefNumber": "123456",
        // "bankreference": "20170101100003485481",
        // "tranParticular": "BillPayment",
        // "paymentMode": "cash",
        // "transactionDate": "01-01-2017 00:00:00",
        // "phonenumber": "254765555136",
        // "debitaccount": "0170100094903",
        // "debitcustname": "HERMAN GITAU NYOTU"

    }
}
