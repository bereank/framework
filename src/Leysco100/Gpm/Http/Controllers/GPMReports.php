<?php

namespace Leysco100\Gpm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Marketing\Models\GMS1;
use Leysco100\Shared\Models\Administration\Models\GMS2;
use Illuminate\Support\Facades\Request as FacadesRequest;


class GPMReports extends Controller
{
    /**
     * Get Scan Logs
     */
    public function getScanLogsByDate(Request $request)
    {
        try {
            $query =  GMS1::with('objecttype', 'creator', 'gates');
            if ($request->query('date')) {
                $query =   $query->whereDate('updated_at', $request->query('date'));
            }

            if ($request->query('month')) {
                $query =   $query->whereMonth('updated_at', $request->query('month'))
                    ->whereYear('updated_at', $request->query('year'));
            }

            $data = $query
                ->orderBy('id', 'desc')
                ->get();
            foreach ($data as $key => $val) {
                $val->objecttype = APDI::where('ObjectID', $val['ObjType'])->first();
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function getSingleScanLogs($id)
    {
        try {
            $data = GMS1::with('objecttype', 'creator')->orderBy('id', 'desc')
                ->where('id', $id)
                ->first();
            $data->creatorName = $data->creator->name;

            $data->scanLogDetails = GMS2::where('DocEntry', $data->id)->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
