<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Carbon\Carbon;


use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\PublicKey;
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
        
        Log::info([$request->all(), $request->header('signature')]);
        $user = User::where('id', 1)->first();
        Auth::login($user);

        $path = __DIR__ . '/../../../resources/public_key.pem';
        $file = file_get_contents($path);
        $publicKey = openssl_pkey_get_public($file);

        $signature = $request->header('signature');

        $isVerified = openssl_verify(json_encode($request->all(), true), $signature, $publicKey);

        if (!$isVerified) {
            Log::info('Signature Not valid' . $isVerified);
            $data = [
                'header' => [
                    'messageID' => "",
                    'originatorConversationID' => "",
                    'statusCode' => '1',
                    'statusMessage' => "Signature Not valid",
                ],
                'responsePayload' => [
                    'transactionInfo' => [
                        'transactionId' => "",
                    ],
                ],
            ];
            //  return response()->json($data);
        }

        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId(218);

        $data = $request->all();

        if (array_key_exists('requestPayload', $data)) {
            if (array_key_exists("additionalData", $data['requestPayload'])) {
                if (array_key_exists("notificationData", $data['requestPayload']['additionalData'])) {
                    $paymentData =   $data['requestPayload']['additionalData']['notificationData'];

                    $transactionDate = Carbon::parse($paymentData['transactionDate']);

                    $payment = [];
                    $payment['ShortCode'] = $paymentData['businessKey'] ?? "";
                    $payment['BusinessKey'] = $paymentData['businessKey'] ?? "";
                    $payment['BusinessKeyType'] = $paymentData['businessKeyType'] ?? "";
                    $payment['MSISDN'] = $paymentData['debitMSISDN'] ?? "";
                    $payment['TransactAmount'] = $paymentData['transactionAmt'] ?? "";
                    $payment['TransactDate'] = $transactionDate ?? "";
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
            Log::error('Logging Query Error' . $th->getMessage());
            $data = [
                'header' => [
                    'messageID' => $messageID,
                    'originatorConversationID' => $originatorConversationID,
                    'statusCode' => '1',
                    'statusMessage' => $th->getMessage(),
                ],
                'responsePayload' => [
                    'transactionInfo' => [
                        'transactionId' => "0001",
                    ],
                ],
            ];
            return response()->json($data);
        }
    }
    public function kcbPaymentQuery(Request $request)
    {
        Log::info("____________PAYMENT QUERY _______________________");
        Log::info([json_encode($request->all(), true), gettype(json_encode($request->all(), true))]);
        $user = User::where('id', 1)->first();
        Auth::login($user);

        $path = __DIR__ . '/../../../resources/public_key.pem';
        $publicKey = file_get_contents($path);
        $publicKey = openssl_pkey_get_public($publicKey);

        $signature = $request->header('signature');

        $isVerified = openssl_verify(json_encode($request->all(), true), $signature, $publicKey);

        if (!$isVerified) {
            Log::info('Signature Not valid' . $isVerified);
            $data = [
                'header' => [
                    'messageID' => "",
                    'originatorConversationID' => "",
                    'statusCode' => '1',
                    'statusMessage' => "Signature Not valid",
                ],
                'responsePayload' => [
                    'transactionInfo' => [
                        'transactionId' => "",
                    ],
                ],
            ];
            //return response()->json($data);
        }
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
            Log::error('Logging Query Error' . $th->getMessage());
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
