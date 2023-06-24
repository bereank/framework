<?php

namespace Leysco\Gpm\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Leysco\GatePassManagementModule\Models\MobileNavBar;
use Leysco\LS100SharedPackage\Services\ApiResponseService;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GPMGate;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OADM;

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
