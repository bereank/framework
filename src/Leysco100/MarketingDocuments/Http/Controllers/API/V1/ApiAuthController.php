<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Leysco100\Shared\Models\GpsSetup;
use Leysco100\Shared\Models\MobileNavBar;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\Gpm\Models\GPMGate;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBIN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;


class ApiAuthController extends Controller
{

    public function webFrontendLogin(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->orWhere('phone_number', $request->email)->first();
        //        return response()->json(["data" => $user]);

        if (!$user) {
            return response(['message' => 'User not found', "statusCode" => 1]);
        }

        $check = !Hash::check($request->password, $user->password);

        if ($check) {
            return response(['message' => 'Invalid Credentials', "statusCode" => 1]);
        }

        //check if user is active

        //        if ($user->status == 0){
        //            return response(['message' => 'User is inactive contact admin',"statusCode"=>1]);
        //        }

        //        if ($user->id != 1) {
        //            $user->tokens()->delete();
        //        }

        $user->oadm = OADM::where('id', 1)->first();
        $accessToken = $user->createToken('authToken')->plainTextToken;

        return response([
            "statusCode" => 0,
            'access_token' => $accessToken,
            'isAbleToPostPayment' => 1,
            'clientLocationRadius' => 70,
            'checkIfWithinRadius' => 0,
            'user' => $user,
            'menuNavigation' => (new AuthorizationService())->mobileNavBar($user->id),
            'userdata' => User::where('type', 'NU')->with('oudg.branch')->get(),
            'branches' => (new AuthorizationService())->getCurrentLoginUserBranches($user->id),
        ]);
    }

    /**
     * For MOBILE AND INTEGRATOT
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response(['message' => 'Invalid Credentials', 'statusCode' => 1]);
        }

        $check = !Hash::check($request->password, $user->password);

        if ($check) {
            return response(['message' => 'Invalid Credentials', 'statusCode' => 2]);
        }

        //check user status
        //        if ($user->status == 0){
        //            return response(['message' => 'User is inactive contact admin','statusCode'=>3]);
        //        }

        $user->oadm = OADM::where('id', 1)->first();
        $accessToken = $user->createToken('authToken')->plainTextToken;

        return response([
            'statusCode' => 0,
            'access_token' => $accessToken,
            'isAbleToPostPayment' => 1,
            'clientLocationRadius' => 70,
            'checkIfWithinRadius' => 0,
            'user' => $user,
            //            'branches' => (new AuthorizationService())->getCurrentLoginUserBranches($user->id),
            //            'menuNavigation' => (new AuthorizationService())->mobileNavBar($user->id),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        //$request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * COMPANY SETUP
     */
    public function companySetupData()
    {
        $settings = OADM::first();
        $loginUser = Auth::user();


        if (!$loginUser->status) {
            return (new ApiResponseService())->apiFailedResponseService("Your account has been deactivated.Please contact your Admin");
        }

        $default_bin = User::where('id', $loginUser->id)->with('oudg')->first();
        if ($default_bin->oudg) {
            $defaultwarehouse = OWHS::where('id', $default_bin->oudg->Warehouse)
                ->with(['binlocations' => function ($query) use ($default_bin) {
                    $query->where('id', $default_bin->oudg->DftBinLoc)
                        ->select(
                            "id",
                            "AbsEntry",
                            "BinCode",
                            "WhsCode",
                            "SysBin"
                        );
                }])
                ->select(
                    "id",
                    "WhsCode",
                    "WhsName",
                    "BinActivat",
                    "BinSeptor"
                )
                ->first();
        }
        $loginUser->gateData = GPMGate::where('id', $loginUser->gate_id)->first();
        $data = [
            'PswdChangeOnReset' =>  $settings->PswdChangeOnReset,
            'HasOtpVerification' => $settings->HasOtpVerification,
            'ExtBucket' => [
                'accessKey' => $settings->ExtBucketAccessID,
                'secretKey' => $settings->ExtBucketSecretKey,
                'destDir' => $settings->ExtBucketDestDir,
                'bucket' => $settings->ExtBucket,
                'region' => $settings->ExtBucketRegion,
            ],
            'gateMaximumRadius' => 70,
            'userData' => $loginUser,
            'menuNavigation' => (new AuthorizationService())->mobileNavBar(),
        ];

        return response([
            'MaximumAllowedPriceAgeInMin' => 25,
            'isAbleToPostPayment' => 1,
            'clientLocationRadius' => 70,
            'checkIfWithinRadius' => 0,
            'menuNavigation' => (new AuthorizationService())->mobileNavBar(),
            'gpsSetttings' => $this->getWorkDays(),
            'defaultwarehouse' => $defaultwarehouse ?? [],
            'ResultState' => true,
            'ResultCode' => 1200,
            'ResultDesc' => "Operation Was Successful",
            'ResponseData' => $data,
        ]);
    }

    /**
     * GPS SETTINGS
     */
    public function getWorkDays()
    {
        try {
            $worKdays = GpsSetup::with(['workDays:id,gps_setup_id,dayName,start_time,end_time'])
                ->select('id', 'max_latitude', 'min_latitude', 'max_longitude', 'min_longitude', 'start_time', 'end_time')
                ->first();

            $weekdays = "1,2,3,4,5,6";
            if ($worKdays) {
                $worKdays->weekdays = $weekdays;
            }
            return $worKdays;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public function promptPasswordChange(Request $request)
    {

        $request->validate([
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request['password']);
        $user->password_changed = true;
        $user->save();

        return response()->json([
            'message' => 'Successfully Changed',
            'redirectUrl' => '/dashboard'
        ]);
    }

    public function gpmMobileNavBar()
    {
        return  MobileNavBar::select('title', 'key')->get();
    }
    public function updateExtBucket(Request $request)
    {

        $validatedData = $request->validate([
            'ExtBucketAccessID' => 'nullable|string|max:255',
            'ExtBucketSecretKey' => 'nullable|string|max:255',
            'ExtBucketDestDir' => 'nullable|string|max:255',
            'ExtBucket' => 'nullable|string|max:255',
            'ExtBucketRegion' => 'nullable|string|max:255',
        ]);

        try {
            $extBuckt = OADM::findOrFail(1);
            $extBuckt->ExtBucketAccessID = $validatedData['ExtBucketAccessID'];
            $extBuckt->ExtBucketSecretKey = $validatedData['ExtBucketSecretKey'];
            $extBuckt->ExtBucketDestDir = $validatedData['ExtBucketDestDir'];
            $extBuckt->ExtBucket = $validatedData['ExtBucket'];
            $extBuckt->ExtBucketRegion = $validatedData['ExtBucketRegion'];
            $extBuckt->save();
            return (new ApiResponseService())->apiSuccessResponseService($extBuckt);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
