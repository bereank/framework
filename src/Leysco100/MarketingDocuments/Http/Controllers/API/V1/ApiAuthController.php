<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Leysco100\Shared\Models\GpsSetup;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;


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
        return response([
            'MaximumAllowedPriceAgeInMin' => 25,
            'isAbleToPostPayment' => 1,
            'clientLocationRadius' => 70,
            'checkIfWithinRadius' => 0,
            'menuNavigation' => (new AuthorizationService())->mobileNavBar(),
            'gpsSetttings' => $this->getWorkDays(),
        ]);
    }

    /**
     * GPS SETTINGS
     */
    public function getWorkDays()
    {
        $worKdays = GpsSetup::with(['workDays:id,gps_setup_id,dayName,start_time,end_time'])
            ->select('id', 'max_latitude', 'min_latitude', 'max_longitude', 'min_longitude', 'start_time', 'end_time')
            ->first();

        $weekdays = "1,2,3,4,5,6";
        if ($worKdays){
            $worKdays->weekdays = $weekdays;
        }
        return $worKdays;
    }
}
