<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\MobileNavBar;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Marketing\Models\GPMGate;
use Leysco100\Shared\Models\Administration\Models\OADM;



class AppSettingsController extends Controller
{
    /**
     * APP Settings
     */
    public function __invoke()
    {
        try {

            $settings = OADM::first();
            $loginUser = Auth::user();
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
                'menuNavigation' => $this->mobileNavBar(),
            ];
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Mobile Menu
     */

    public function mobileNavBar()
    {
        // return [
        //     [
        //         "title" => "Release Goods",
        //         "key" => "home",
        //     ],
        //     [
        //         "title" => "Pending Documents",
        //         "key" => "pendingdocuments",
        //     ],
        //     [
        //         "title" => "Scan Logs",
        //         "key" => "scanlogs",
        //     ],
        //     [
        //         "title" => "Gates",
        //         "key" => "gates",
        //     ]
        // ];
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
