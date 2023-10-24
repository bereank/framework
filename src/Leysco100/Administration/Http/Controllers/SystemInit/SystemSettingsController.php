<?php

namespace App\Http\Controllers\API\Administration\Setup\SystemInit;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Administration\Http\Controllers\Controller;

class SystemSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OADM::with('opln')->where('id', 1)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $details = [
                'CostPrcLst' => $request['CostPrcLst'],  //Based Price Origin
                'DfSVatItem' => $request['DfSVatItem'], //Sales Tax Groups Items
                'DfSVatServ' => $request['DfSVatServ'], //Sales Tax Groups Service
                'DfPVatItem' => $request['DfPVatItem'],  //Purchase Tax Groups Items
                'DfPVatServ' => $request['DfPVatServ'], //Purchaes Tax Groups Items
            ];
            OADM::where('id', 1)->update(array_filter($details));
            return (new ApiResponseService())->apiSuccessResponseService("successfully updated.");
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function saveEmailSettings(Request $request)
    {

        try {
            $request->validate(
                [
                    'mail_driver' => 'required|string|max:255',
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|string|max:255',
                    'mail_username' => 'required|string|max:255',
                    'mail_password' => 'required|string|max:255',
                    'mail_encryption' => 'required|string|max:255',
                    'mail_from_address' => 'required|string|max:255',
                    'mail_from_name' => 'required|string|max:255',
                ]
            );

            $settings = [
                'MAIL_DRIVER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_NAME' => $request->mail_from_name,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            ];

            return (new ApiResponseService())->apiSuccessResponseService("Setting successfully updated.");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
