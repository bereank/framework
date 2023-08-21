<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;
use Leysco100\Gpm\Mail\GPMScanLogs;


use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Leysco100\Gpm\Jobs\ExtDocsSyncJob;
use Leysco100\Gpm\Mail\GPMSapDocuments;
use Leysco100\Gpm\Reports\ExportScanLog;
use Leysco100\Gpm\Reports\ExportSapDocuments;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;

use Leysco100\Shared\Models\Administration\Models\OADM;




class GMPDocumentController extends Controller
{

    /**
     * Create Gatement Magement Documents
     */
    public function __invoke(Request $request)
    {

        dispatch(new ExtDocsSyncJob($request['data']));
    }

    public function export_scan_logs()
    {
        Excel::store(new ExportScanLog(), 'ExportScanLogReport.xlsx');
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);
        try {
            Mail::to($emails)->send(new GPMScanLogs());
            $response = [
                "message" => "Scan log Report sent to $emailString",
                "data" => $emailString
            ];
            return (new ApiResponseService())->apiSuccessResponseService($response);
        } catch (\Exception $e) {
            return (new ApiResponseService())->apiFailedResponseService($e);
        }
    }

    public function export_sap_documents()
    {
        Excel::store(new ExportSapDocuments(), 'ExportSapDocuments.xlsx');
        $emailString = OADM::where('id', 1)->value("NotifEmail");
        $emails = explode(';', $emailString);
        Mail::to($emails)->send(new GPMSapDocuments());
        return response()->json(["message" => "Scan log Report sent to $emailString"]);
    }
}
