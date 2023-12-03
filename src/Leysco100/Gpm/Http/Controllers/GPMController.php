<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Services\AuthorizationService;
use Leysco100\Shared\Models\Gpm\Models\GMS1;
use Leysco100\Shared\Models\Gpm\Models\GMS2;
use Leysco100\Shared\Models\Gpm\Models\OGMS;

class GPMController extends Controller
{

    public function getGPMDocuments(Request $request)
    {

        // $ObjType = 300;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);

            $data = OGMS::with('objecttype');
            // Apply search filters
            if ($request->has('search')) {
                $searchTerm = $request->input('search');

                if (strlen($searchTerm) >= 3) {
                    $status = "";
                    $searchTerm = strtolower($searchTerm);
                    if ($searchTerm == 'released') {
                        $status = 1;
                    }
                    if ($searchTerm == 'open') {
                        $status = 0;
                    }
                    $search = $request->input('search');
                    $searchDate = date('Y-m-d', strtotime($searchTerm));
                    $data = $data->where(function ($query) use ($searchTerm, $searchDate,  $search, $status) {
                        $query->orWhere('DocNum', 'LIKE', "%{$search}%")
                            ->orWhereDate('created_at', 'LIKE', "%{$search}%")
                            ->orWhere('DocTotal', 'LIKE', "%{$search}%")
                            ->orWhere('ExtRefDocNum', 'LIKE', "%{$search}%")
                            ->orWhere('Status', 'LIKE', "%{$search}%")
                            ->orWhereDate('GenerationDateTime', 'LIKE', "%{$search}%");
                    });
                }
            }

            $data = $data->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function getScanLogs(Request $request)
    {

        // $ObjType = 301;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            // $data = GMS1::with('objecttype', 'creator', 'gates')
            //     ->orderBy('id', 'desc')
            //     ->take(5000)
            //     ->get();

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);

            $data = GMS1::with([
                'objecttype:id,ObjectID',
                'creator:id,name,account',
                'gates:id,Name,location_id'
            ])
                ->select('id', 'ObjType', 'DocNum', 'UserSign', 'GateID', 'Status', 'Released', 'created_at');

            // Apply search filters
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                if (strlen($searchTerm) >= 3) {
                    $searchDate = date('Y-m-d', strtotime($searchTerm));
                    $data = $data->where(function ($query) use ($searchTerm, $searchDate) {
                        $query->where('DocNum', 'LIKE', "%{$searchTerm}%")
                            ->orWhereDate('created_at', 'LIKE', "%{$searchDate}%")
                            // ->orWhereDate('updated_at', 'LIKE', "%{$searchDate}%")
                            ->orWhereHas('objecttype', function ($query) use ($searchTerm) {
                                $query->where('ObjectID', 'LIKE', "%{$searchTerm}%");
                            })
                            ->orWhereHas('creator', function ($query) use ($searchTerm) {
                                $query->where('name', 'LIKE', "%{$searchTerm}%")
                                    ->orWhere('account', 'LIKE', "%{$searchTerm}%");
                            })
                            ->orWhereHas('gates', function ($query) use ($searchTerm) {
                                $query->where('Name', 'LIKE', "%{$searchTerm}%");
                            });
                    });
                }
            }
            $data = $data->latest()
                ->paginate($perPage, ['*'], 'page', $page);

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

        // $ObjType = 301;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = GMS1::with('objecttype', 'creator')->orderBy('id', 'desc')
                ->where('id', $id)
                ->select('id', 'ObjType', 'DocNum', 'UserSign', 'GateID', 'Status', 'Released', 'created_at')
                ->first();
            $data->creatorName = $data->creator->name;

            $data->scanLogDetails = GMS2::where('DocEntry', $data->id)->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
