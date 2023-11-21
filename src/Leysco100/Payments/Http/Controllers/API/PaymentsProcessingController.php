<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Carbon\Carbon;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Payments\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Services\DocumentsService;

class PaymentsProcessingController extends Controller
{
    public function kcbPaymentNotification(Request $request)
    {

        Log::info("____________KCB PAYMENT NOTIFICATION _______________________");
        Log::info([$request->all(), $request->header('signature')]);

        $user = User::where('id', 1)->first();
        Auth::login($user);

        $path = __DIR__ . '/../../../resources/kcb_uat_publickey.pem';
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
            return response()->json($data);
        }

        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId(218);

        $data = $request->all();
        $payment = [];
        if (array_key_exists('requestPayload', $data)) {
            if (array_key_exists("additionalData", $data['requestPayload'])) {
                if (array_key_exists("notificationData", $data['requestPayload']['additionalData'])) {
                    $paymentData =   $data['requestPayload']['additionalData']['notificationData'];

                    $transactionDate = Carbon::parse($paymentData['transactionDate']);

                    $payment['BusinessShortCode'] = $paymentData['businessKey'] ?? "";
                    $payment['BusinessKey'] = $paymentData['businessKey'] ?? "";
                    $payment['BusinessKeyType'] = $paymentData['businessKeyType'] ?? "";
                    $payment['MSISDN'] = $paymentData['debitMSISDN'] ?? "";
                    $payment['TransAmount'] = $paymentData['transactionAmt'] ?? 0;
                    $payment['TransTime'] = $transactionDate ?? now();
                    $payment['TransID'] = $paymentData['transactionID'] ?? "";
                    $payment['FirstName'] = $paymentData['firstName'] ?? "";
                    $payment['MiddleName'] = $paymentData['middleName'] ?? "";
                    $payment['LastName'] = $paymentData['lastName'] ?? "";
                    $payment['Currency'] = $paymentData['currency'] ?? "";
                    $payment['Dscription'] = $paymentData['narration'] ?? "";
                    $payment['TransactType'] = $paymentData['transactionType'] ?? "";
                    $payment['Balance'] = $paymentData['transactionAmt'] ?? 0;
                    $payment['DocNum'] =  $Numbering['NextNumber'];
                    $payment['Source'] = 1;
                    $payment['ObjType'] = 218;
                }
            }
        }


        $originatorConversationID = $data['header']['originatorConversationID'] ?? '';
        $messageID = $data['header']['messageID'] ?? '';

        try {
            if ($payment) {
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
            } else {
                Log::info('Error: Request Could not be parsed');
                $data = [
                    'header' => [
                        'messageID' => $messageID,
                        'originatorConversationID' => $originatorConversationID,
                        'statusCode' => '1',
                        'statusMessage' => "Request Could not be parsed",
                    ],
                    'responsePayload' => [
                        'transactionInfo' => [
                            'transactionId' => "0001",
                        ],
                    ],
                ];
                return response()->json($data);
            }
        } catch (\Throwable $th) {
            Log::info('Logging Query Error' . $th->getMessage());
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
        Log::info("____________EQB VALIDATION  _______________________");
        Log::info($request->all());

        $this->eqbAuth($request);
        if (!Auth::check()) {
            $data =   [
                "amount" => 0,
                "billNumber" => "",
                "billName" => "",
                "description" => "Invalid Credentials",
                "type" => "1"
            ];
            return response()->json($data);
        }
        if (empty($request['account'])) {
            return response()->json([
                "amount" => 0,
                "billNumber" => "null",
                "billName" => "null",
                "description" => "bill number not found",
                "type" => "1"
            ]);
        }

        try {
            $data = [
                "amount" => "0.0",
                "billName" => "Test_Account_1",
                "billNumber" => "10192",
                "billerCode" => "102345",
                "createdOn" => "2023-11-01",
                "currencyCode" => "KES",
                "customerName" => "Test_Account_1",
                "customerRefNumber" => "10192",
                "description" => "buy goods",
                "dueDate" => "2023-11-01",
                "expiryDate" => "2023-11-01",
                "Remarks" => "purchase",
                "type" => "1"
            ];
            return response()->json([$data]);
        } catch (\Throwable $th) {
            Log::error('Validation Error' . $th->getMessage());
            return response()->json([
                "amount" => 0,
                "billNumber" => "null",
                "billName" => "null",
                "description" => $th->getMessage(),
                "type" => "1"
            ]);
        }
    }

    public function eqbPaymentNotification(Request $request)
    {

        Log::info("____________EQB PAYMENT NOTIFICATION  _______________________");
        Log::info($request->all());

        $this->eqbAuth($request);
        if (!Auth::check()) {
            $data =   [
                "amount" => 0,
                "billNumber" => "",
                "billName" => "",
                "description" => "Invalid Credentials",
                "type" => "1"
            ];
            return response()->json($data);
        }
        try {
            $Numbering = (new DocumentsService())
                ->getNumSerieByObjectId(218);

            $paymentData =  $request->all();

            $exists = OCRP::where('BankRefNo', $paymentData['bankreference'])->exists();

            if ($exists) {
                return response()->json([
                    "responseCode" => "OK",
                    "responseMessage" => "DUPLICATE TRANSACTION"
                ]);
            }
            $transactionDate = Carbon::parse($paymentData['transactionDate']);
            $payment = [];

            $payment['BusinessKey'] = $paymentData['billNumber'] ?? "";
            $payment['TransAmount'] = $paymentData['billAmount'] ?? 0;
            $payment['FirstName'] = $paymentData['debitcustname'] ?? "";
            $payment['MSISDN'] = $paymentData['phonenumber'] ?? "";
            $payment['Dscription'] = $paymentData['tranParticular'] ?? "";
            $payment['TransTime'] = $transactionDate ?? now();
            $payment['TransactType'] = $paymentData['paymentMode'] ?? "";
            $payment['ContactName'] = $paymentData['debitcustname'] ?? "";
            $payment['debitAccNo'] = $paymentData['debitaccount'] ?? "";
            $payment['TransID'] = $paymentData['bankreference'] ?? "";
            $payment['DocNum'] =  $Numbering['NextNumber'];
            $payment['BusinessShortCode'] = null;
            $payment['Balance'] =  $paymentData['billAmount'] ?? 0;
            $payment['TransID'] =   $paymentData['bankreference'] ?? "";
            $payment['Source'] = 2;
            $payment['ObjType'] = 218;

            $transaction =   OCRP::create($payment);

            (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);

            return response()->json([
                "responseCode" => "OK",
                "responseMessage" => "SUCCESSFUL"
            ]);
        } catch (\Throwable $th) {
            Log::error('Payment Notification Error' . $th->getMessage());
            return response()->json([
                "responseCode" => "OK",
                "responseMessage" => $th->getMessage()
            ]);
        }
    }

    public function eqbAuth($request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->username)
                ->orWhere('name', $request->username)->first();

            if (!$user) {
                $data =   [
                    "amount" => 0,
                    "billNumber" => "",
                    "billName" => "",
                    "description" => "UserName Does Not Exist",
                    "type" => "1"
                ];
                return response()->json($data);
            }
            $check = !Hash::check($request->password, $user->password);
            if ($check) {
                $data =   [
                    "amount" => 0,
                    "billNumber" => "",
                    "billName" => "",
                    "description" => "Invalid Password",
                    "type" => "1"
                ];
                return response()->json($data);
            } else {
                Auth::login($user);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            $data =   [
                "amount" => 0,
                "billNumber" => "",
                "billName" => "",
                "description" => $th->getMessage(),
                "type" => "1"
            ];
            return response()->json($data);
        }
    }
}
