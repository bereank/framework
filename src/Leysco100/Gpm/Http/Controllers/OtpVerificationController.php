<?php

namespace Leysco100\Gpm\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\OtpVerification;
use Leysco100\Shared\Services\ApiResponseService;



class OtpVerificationController extends Controller
{

    public function SendOTPVerificationCode(Request $request)
    {
        try {
            $phone = "255" . substr($request['phone_number'], -9);

            //generate otp
            $otp = mt_rand(100000, 999999);

            //get credentials from config file
//            config('gate-pass-management-module.username');
//            config('gate-pass-management-module.password');
//            config('gate-pass-management-module.sender_id');
            $username = "cargen";
            $password = "42987dn89.!8ue2";
            $destination = $phone;
            $sender_id = "CARGEN";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://www.sms.co.tz/api.php?do=sms&username=' . $username . '&password=' . $password . '&senderid=' . $sender_id . '&dest=' . $destination . '&msg=' . $otp,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',

            ));

            $response = curl_exec($curl);

            $response_data = explode(",",$response);
            if ($response_data[0] == "ERR"){
                return (new ApiResponseService())->apiFailedResponseService($response);
            }

            $UserSign = Auth::user();
            $GateId = $UserSign->gate_id ?? 1;
            $phone_number = $request['phone_number'];
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            try {
                DB::connection("tenant")->beginTransaction();

                // perform database operations here
                $inserted_id =   DB::connection("tenant")->table('otp_verification')->insertGetId([
                    "UserSign" => $UserSign->id,
                    "GateId" => $GateId,
                    "phone_number" => $phone_number,
                    "otp_code" => $otp,
                    "status" => 1,
                    "expires_at" => $expires_at,
                    "sms_response" => $response,
                    "created_at" => date('Y-m-d H:i:s'),
                ]);
                DB::connection("tenant")->commit();
            } catch (\Exception $e) {
                DB::connection("tenant")->rollback();
                return (new ApiResponseService())->apiFailedResponseService($e);
                // handle the exception
            }
            curl_close($curl);
            return (new ApiResponseService())->apiSuccessResponseService($response);
        } catch (\Exception $e) {
            $request['phone_number'] = null;
            return (new ApiResponseService())->apiFailedResponseService($e);
            // handle the exception
        }
    }


    public static function send($data)
    {

        $api_username = Auth::user()->api_username;
        $api_key = Auth::user()->api_key;
        $sender_id = Auth::user()->sender_id;
        // dd($api_username, $api_key, $sender_id);
        $gateway = new AfricasTalkingApi($api_username, $api_key);
        $client = User::find($data->client_id);

        if (!$client) {
            return response()->json(['result_code' => 3, 'message' => "Invalid Session Client ID. Login Again", 'data' => $data]);
        }

        $toNumbers = $data->toNumbers;
        $toNames = $data->toNames;
        $message = $data->message;


        try {
            $results = $gateway->sendMessage($toNumbers, $message, $sender_id);
            $last_error = "";
            $cost = 0;
            foreach ($results as $result) {
                if ($result->status == "Success") {
                    // status is either "Success" or "error message"
                    $new_cost = str_replace('KES', '', $result->cost);
                    $new_cost = round($new_cost);
                    $new_cost = ceil($new_cost);
                    $variable_new = $new_cost;
                    $cost += $variable_new;
                } else {
                    $last_error = $result->status . ": Error Code: " . $result->statusCode;
                }
            }

            if ($cost > 0) {
                return (new ApiResponseService())->apiSuccessResponseService($data);
            } else {
                return (new ApiResponseService())->apiFailedResponseService($data);
            }
        } catch (AfricasTalkingGatewayException $e) {
            //dd("Error: ".$e->getMessage());
            return response()->json(['result_code' => 1, 'message' => "Error: " . $e->getMessage(), 'data' => $data]);
        }
    }

    public function VerifyOTP(Request $request)
    {
        //get otp
        $otp = $request['otp_code'];
        $phone_number = $request['phone_number'];

        try {
            $last =  DB::connection("tenant")->table('otp_verification')
                ->where('phone_number', $phone_number)
                ->where('UserSign', Auth::user()->id)
                ->latest('created_at')->first();
        } catch (\Exception $e) {
            // handle the exception
            $last = null;
            return (new ApiResponseService())->apiFailedResponseService($e);
        }


        // if ($last->status == 2) {
        //     return (new ApiResponseService())->apiFailedResponseService(['OTP CODE Already Verified']);
        // }
        if ($last == null) {
            return (new ApiResponseService())->apiFailedResponseService([$request, 'Error on phone number']);
        }

        if ($last->expires_at < date('Y-m-d H:i:s')) {

            DB::connection("tenant")->table('otp_verification')
                ->where('id', $last->id)
                ->where('UserSign', Auth::user()->id)
                ->limit(1)  //to ensure only one record is updated.
                ->update(array('status' => 3));
            return (new ApiResponseService())->apiFailedResponseService([$request, 'OTP CODE EXPIRED']);
        }





        if ($last->otp_code == $otp) {
            try {
                DB::connection("tenant")->table('otp_verification')
                    ->where('id', $last->id)
                    ->where('UserSign', Auth::user()->id)
                    ->limit(1)  //to ensure only one record is updated.
                    ->update(array('status' => 2));
                return (new ApiResponseService())->apiSuccessResponseService([$request, 'OTP CODE VERIFIED SUCCESSFULLY', 200]);
            } catch (\Exception $e) {
                // handle the exception

                return (new ApiResponseService())->apiFailedResponseService($e);
            }
        } else {
            try {
                DB::connection("tenant")->table('otp_verification')
                    ->where('id', $last->id)
                    ->where('UserSign', Auth::user()->id)
                    ->limit(1)  //to ensure only one record is updated.
                    ->update(array('status' => 3));

                return (new ApiResponseService())->apiFailedResponseService([$request, 'INVALID OTP CODE']);
            } catch (\Exception $e) {
                // handle the exception

                return (new ApiResponseService())->apiFailedResponseService($e);
            }
        }
    }

    public function ShowOtpVerifications(Request $request)
    {
        try {
            $query =   OtpVerification::with('creator');
            if ($request->query('user_id')) {
                $query = $query->where('UserSign', $request['user_id']);
            }
            if ($request->query('id')) {
                $query = $query->where('id', $request['id']);
            }
            $data = $query->get();
        } catch (\Exception $e) {
            $request['phone_number'] = null;
            return (new ApiResponseService())->apiFailedResponseService($e);
            // handle the exception
        }
        return (new ApiResponseService())->apiSuccessResponseService($data);
    }
}
