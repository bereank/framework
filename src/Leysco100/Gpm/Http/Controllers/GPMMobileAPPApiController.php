<?php

namespace Leysco100\Gpm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Gpm\Jobs\SendEmailJob;
use Leysco100\Gpm\Services\BackupModeService;
use Leysco100\Gpm\Services\FormFieldsService;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Models\Gpm\Models\BackUpModeLines;
use Leysco100\Shared\Models\Gpm\Models\BackUpModeSetup;
use Leysco100\Shared\Models\Gpm\Models\GMS1;
use Leysco100\Shared\Models\Gpm\Models\GMS2;
use Leysco100\Shared\Models\Gpm\Models\OGMS;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OADM;


class GPMMobileAPPApiController extends Controller
{
    /** GET ALL DOCUMENTS */
    public function index()
    {

        try {
            $allData = OGMS::where('Status', 2)->get();

            foreach ($allData as $key => $record) {
                $lineDetails = explode('|', $record->LineDetails);
                $itemRows = [];
                foreach ($lineDetails as $key => $value) {
                    $data = explode(';', $value);

                    $item = [
                        'ItemCode' => $data[0],
                        'Quantity' => $data[1],
                    ];

                    array_push($itemRows, $item);
                }

                $record->LineDetails = $itemRows;
            }
            return (new ApiResponseService())->apiSuccessResponseService($allData);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'DocNum' => 'required',
        ]);


        $user = Auth::user();
        $emailString = OADM::where('id', 1)->value("NotifEmail");

        $emails = explode(';', $emailString);
        DB::connection("tenant")->beginTransaction();
        try {
            $fullDocNum = explode("-", $request['DocNum']);

            if (count($fullDocNum) < 3) {
                return response()
                    ->json(
                        [
                            'message' => "The given data was invalid",
                            'resultCode' => 1500,
                            'BackUpMode' => 0,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                                'error' => 'String too short'
                            ],
                        ],
                        200
                    );
            }
            $originSystem = $fullDocNum[0];
            $ObjTypeString = $fullDocNum[1];
            $DocNum = $fullDocNum[2];
            // $Obj = APDI::where('ObjAcronym', $ObjTypeString)->select('ObjectID')->first();

            // $ObjType = $Obj->ObjectID;

            if ($ObjTypeString == "AR") {
                $ObjType = 13;
            }
            if ($ObjTypeString == "DN") {
                $ObjType = 15;
            }

            if ($ObjTypeString == "IM") {
                $ObjType = 67;
            }
            if ($ObjTypeString == "DISPNOT") {
                $ObjType = "DISPNOT";
            }

            if ($ObjTypeString == "DS") {
                $ObjType = "DISPNOT";
            }



            $scanLogData = [
                "ObjType" => $ObjType,
                "DocNum" => $request['DocNum'],
                "Location" => $request['Location'],
                "Longitude" => $request['Longitude'],
                "Latitude" => $request['Latitude'],
                "AttachPath" => $request['AttachPath'],
                'UserSign' => $user->id,
                'GateID' => $user->gate_id,
                "Phone" => $request['Phone'],
            ];
            $newRecord = new GMS1($scanLogData);
            $newRecord->save();
            DB::connection("tenant")->commit();
            if ($request['fields']) {
                $this->postScanLogDetails($request['fields'], $newRecord->id);
            }
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();

            Log::info($th);
            return response()
                ->json(
                    [
                        'message' => "The given data was invalid",
                        'resultCode' => 1500,
                        'BackUpMode' => 0,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
                            'Error' => $th,
                        ],
                    ],
                    200
                );
        }
        /**
         * Verify if the item Exist in
         */
        //        $record = OGMS::where('ObjType', $ObjType)
        //            ->where(function ($q) use ($originSystem, $DocNum) {
        //                if ($originSystem == "LS100") {
        //                    $q->where('DocNum', $DocNum);
        //                }
        //                if ($originSystem == "SAP") {
        //                    $q->where('ExtRefDocNum', $DocNum);
        //                }
        //            })
        //            ->first();

        /**
         * Check The Base Document
         */

        $record = OGMS::where('ObjType', $ObjType)
            ->where(function ($q) use ($originSystem, $DocNum) {
                if ($originSystem == "LS100") {
                    $q->where('DocNum', $DocNum);
                }
                if ($originSystem == "SAP") {
                    $q->where('ExtRefDocNum', $DocNum);
                }
            })
            ->first();

        $isBackupMode = false;

        if (BackUpModeLines::where('DocNum', $fullDocNum[2])->where('ObjType', $ObjType)->where('ReleaseStatus', 1)->exists()) {
            return response()
                ->json(
                    [
                        'message' => "Document Already Released Under backup mode",
                        'resultCode' => 1500,
                        'BackUpMode' => 1,
                        'type' =>  'duplicate',
                        'docnum' => (string)$DocNum ?? '0',
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',

                        ],
                    ],
                    200
                );
        }
        //Document Doest Not Exist
        if (!$record) {

            $newRecord->update([
                'Status' => 1,
            ]);

            // Check if backup mode is on
            $isBackupMode = (new BackupModeService())->isBackupMode();

            if (!$isBackupMode) {
                dispatch(new SendEmailJob($emails, $newRecord->id));
                return response()
                    ->json(
                        [
                            'message' => "Document Doest Not Exist",
                            'resultCode' => 1500,
                            'BackUpMode' => 0,
                            'type' => 'notfound',
                            'docnum' => (string)$DocNum ?? '0',
                            'ScanLogId' => $newRecord->id,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                        ],
                        200
                    );
            }

            if ($isBackupMode) {
                $Fields = (new FormFieldsService())->getFormFields();

                $data = json_decode($Fields, true);

                if (is_array($data)) {
                    $requiredFields = array_filter($data, function ($item) {
                        return isset($item['mandatory']) && $item['mandatory'] === 'Y';
                    });
                    if (!is_array($requiredFields)) {
                        $requiredFields = [];
                    }
                } else {
                    $requiredFields = [];
                }

                $fieldsrequired = array_column(collect($requiredFields)->toArray(), 'key');
                $submitted = isset($request['fields']) && is_array($request['fields']) ? array_column($request['fields'], 'key') : [];

                if (count($requiredFields) > count($submitted)) {
                    return response()->json([
                        'message' => 'missing required fields',
                        'resultCode' => 1500,
                        "resultDesc" => array_diff($fieldsrequired, $submitted)
                    ]);
                }

                $scanTime = GMS1::where('DocNum', $request['DocNum'])->count();
                if ($scanTime >= 3) {
                    dispatch(new SendEmailJob($emails, $newRecord->id));
                }

                if (BackUpModeLines::where('DocNum', $fullDocNum[2])->where('ObjType', $ObjType)->where('ReleaseStatus', 1)->exists()) {
                    return response()
                        ->json(
                            [
                                'message' => "Document Already Released Under backup mode",
                                'resultCode' => 1500,
                                'BackUpMode' => 1,
                                'type' =>  'duplicate',
                                'docnum' => (string)$DocNum ?? '0',
                                'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                                'errors' => [
                                    'record' => 'Discrepancy Noted- Don’t Release Goods',

                                ],
                            ],
                            200
                        );
                } else {
                    $backupmodeHeader = BackUpModeSetup::where('id', $isBackupMode->id)->first();
                    if ($originSystem == "LS100") {
                        $DocOrigin = 1;
                    }
                    if ($originSystem == "SAP") {
                        $DocOrigin = 2;
                    }
                    $lineData = BackUpModeLines::updateOrCreate(
                        [
                            'DocNum' => $fullDocNum[2],
                            'ObjType' => $ObjType,
                        ],
                        [
                            "BaseType" => 240,
                            "BaseEntry" => $newRecord->id,
                            'DocOrigin' => $DocOrigin,
                            "DocEntry" => $backupmodeHeader->id,
                            // "DocDate" => Carbon::now(),
                            'UserSign' => $user->id,
                        ]
                    );
                    $lineData->save();
                    return response()
                        ->json(
                            [
                                'message' => "Operation Successful",
                                'resultCode' => 1500,
                                'BackUpMode' => 1,
                                'type' => 'notfound',
                                'docnum' => (string)$DocNum ?? '0',
                                'ScanLogId' => $newRecord->id,
                                'resultDesc' => 'Back up mode on: Kindly confirm the Document',
                                'errors' => [
                                    "DocumentDetails" => $lineData,
                                    'record' => "GPM is running on backup-mode",
                                ],
                                "DocumentDetails" => $lineData,
                            ],
                            200
                        );
                }
            }
        }


        if ($record->BaseType && $record->BaseEntry && $record->Status != 3) {
            /**
             * Check The Base Document is closed
             */
            $baseRecord = OGMS::where('ObjType', $record->BaseType)
                ->where('ExtRef', $record->BaseEntry)
                ->with('objecttype')
                ->first();
            if (($baseRecord && $ObjType != 'DISPNOT')) {

                if ($baseRecord->Status == 3) {
                    //    $this->closeOtherDocuments($record->BaseType, $record->BaseEntry);
                    $allInstanceOfDocuments = OGMS::where('ObjType', $baseRecord->ObjType)
                        ->where(function ($q) use ($originSystem, $baseRecord) {
                            if ($originSystem == "LS100") {
                                $q->where('DocNum', $baseRecord->DocNum);
                            }
                            if ($originSystem == "SAP") {
                                $q->where('ExtRefDocNum', $baseRecord->ExtRefDocNum);
                            }
                        })
                        ->get();
                    $record->update([
                        'Status' => 2,
                        'ScanLogID' => $newRecord->id,
                    ]);
                    $newRecord->update([
                        'Status' => 2,
                        'Comment' => "Base Document Closed",
                    ]);
                    $rows = [];
                    foreach ($allInstanceOfDocuments as $Documents) {
                        $itemRows = [];

                        $lineDetails = explode('|',  $Documents->LineDetails);

                        foreach ($lineDetails as $key => $value) {
                            $data = explode(';', $value);

                            $item = [
                                'ItemCode' => $data[0],
                                'Quantity' => $data[1],
                            ];

                            array_push($itemRows, $item);
                        }
                    }
                    $newRow = array_merge($rows, $itemRows);
                    $baseRecord->LineDetails = $newRow;
                    return response()
                        ->json(
                            [
                                'message' => "There exist scanned base document",
                                'resultCode' => 1500,
                                'BackUpMode' => 0,
                                'docnum' => (string)$DocNum ?? '0',
                                'type' =>  'duplicate',
                                'ScanLogId' => $newRecord->id,
                                'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                                'errors' => [
                                    'record' => 'Discrepancy Noted- Don’t Release Goods',
                                ],
                                'DocumentDetails' => $baseRecord,
                            ],
                            200
                        );
                }
            }
        }
        /**
         * status reference
         * 3=Released,2=Scanned But Flagged,1=Scanned But Not Confirmed, 0=Open
         */

        //If the document is not in Open State or Not Confirmed
        if (($record->Status != 0) && ($record->Status != 1)) {

            if ($record->Status == 4) {
                $newRecord->update([
                    'Status' => 4,
                    'ScanLogID' => $record->id,
                ]);
                return response()
                    ->json(
                        [
                            'message' => "The document has been Cancelled",
                            'resultCode' => 1500,
                            'BackUpMode' => 0,
                            'type' => 'duplicate',
                            'docnum' => (string)$DocNum ?? '0',
                            'ScanLogId' => $newRecord->id,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                        ],
                        200
                    );
            } else {
                $newRecord->update([
                    'Status' => 2,
                    'ScanLogID' => $record->id,
                ]);
                dispatch(new SendEmailJob($emails, $newRecord->id));
                return response()
                    ->json(
                        [
                            'message' => "Duplicate Scan",
                            'resultCode' => 1500,
                            'BackUpMode' => 0,
                            'type' => 'duplicate',
                            'docnum' => (string)$DocNum ?? '0',
                            'ScanLogId' => $newRecord->id,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                        ],
                        200
                    );
            }
        }

        $newRecord->update([
            'Status' => 0,
            'DocID' => $record->id,
        ]);

        //Close SubSequent Documents
        // if ($record->BaseType && $record->BaseEntry) {
        //     $this->closeOtherDocuments($record->BaseType, $record->BaseEntry);
        // }

        $record->update([
            'Status' => 1,
            'ScanLogID' => $newRecord->id,
        ]);


        $allInstanceOfDocuments = OGMS::where('ObjType', $ObjType)
            ->where(function ($q) use ($originSystem, $DocNum) {
                if ($originSystem == "LS100") {
                    $q->where('DocNum', $DocNum);
                }
                if ($originSystem == "SAP") {
                    $q->where('ExtRefDocNum', $DocNum);
                }
            })
            ->get();



        $itemRows = [];
        foreach ($allInstanceOfDocuments as $key => $doc) {


            $lineDetails = explode('|', $doc->LineDetails);

            foreach ($lineDetails as $key => $value) {
                $data = explode(';', $value);

                $item = [
                    'ItemCode' => $data[0],
                    'Quantity' => $data[1],
                ];

                array_push($itemRows, $item);
            }
        }


        $singleDocument =  $allInstanceOfDocuments->first();
        $singleDocument->LineDetails = $itemRows;
        return response()
            ->json(
                [
                    'message' => "Operation Successful",
                    'resultCode' => 1200,
                    'ScanLogId' => $newRecord->id,
                    'resultDesc' => 'Kindly confirm the Document',
                    'BackUpMode' => 0,
                    'errors' => [
                        'record' => "",
                        "DocumentDetails" => $singleDocument,
                    ],
                    'details' => [
                        'record' =>  $singleDocument,
                    ],
                    "DocumentDetails" => $singleDocument,
                ],
                200
            );
    }

    public function update(Request $request, $id)
    {

        try {
            if ($request['BackUpMode']) {
                $isBackupMode = (new BackupModeService())->isBackupMode();
                if (!$isBackupMode) {
                    return response()
                        ->json(
                            [
                                'message' => "Back-up mode off. Try scanning again.",
                                'resultCode' => 1500,
                                'resultDesc' => 'Try scanning again',
                                'errors' => [
                                    'record' => 'Try scanning again',
                                ],
                            ],
                            200
                        );
                }
                $data = BackUpModeLines::findorFail($id);
                if ($data->ReleaseStatus == 1) {
                    return response()
                        ->json(
                            [
                                'message' => "The given data was invalid",
                                'resultCode' => 1500,
                                'BackUpMode' => 1,
                                'resultDesc' => 'Discrepancy Noted- Document already released',
                                'Details' => [
                                    'record' => 'Discrepancy Noted- Document already released',

                                ],
                                'errors' => [
                                    'record' => 'Discrepancy Noted- Document already released',
                                ],
                            ],
                            200
                        );
                }
                GMS1::where('id', $data->DocEntry)
                    ->update([
                        'Released' => 1,
                        'Comment'=>$request['comment'] ?? null
                    ]);
                BackUpModeLines::where("id", $id)
                    ->update([
                        'ReleaseStatus' => 1,
                    ]);

                return response()
                    ->json(
                        [
                            'message' => "Customer Can Exit with goods",
                            'resultCode' => 1200,
                            'BackUpMode' => 1,
                            'resultDesc' => 'Customer Can Exit with goods',
                            'Success' => [
                                "DocumentDetails" => $data,
                            ],
                            'errors' => [
                                'record' => "",
                                "DocumentDetails" => $data,
                            ],
                        ],
                        200
                    );
            } else {
                $data = OGMS::findOrFail($id);

                //Close SubSequent Documents
                if ($data->BaseType && $data->BaseEntry) {
                    $this->closeOtherDocuments($data->BaseType, $data->BaseEntry);
                }
                if ($data->Status == 3) {
                    return response()
                        ->json(
                            [
                                'message' => "The given data was invalid",
                                'resultCode' => 1500,
                                'resultDesc' => 'Discrepancy Noted- Document already released',
                                'errors' => [
                                    'record' => 'Discrepancy Noted- Document already released',
                                ],
                            ],
                            200
                        );
                }
                OGMS::where('ObjType', $data->ObjType)
                    ->where('ExtRefDocNum', $data->ExtRefDocNum)
                    ->update([
                        'Status' => 3,
                    ]);

                GMS1::where('id', $data->ScanLogID)
                    ->update([
                        'Released' => 1,
                        'Comment'=>$request['comment'] ?? null
                    ]);

                return response()
                    ->json(
                        [
                            'message' => "Customer Can Exit with goods",
                            'resultCode' => 1200,
                            'resultDesc' => 'Customer Can Exit with goods',
                            'errors' => [
                                'record' => "",
                                "DocumentDetails" => $data,
                            ],
                        ],
                        200
                    );
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function closeOtherDocuments($ObjType, $DocEntry)
    {
        $records = OGMS::where('ObjType', $ObjType)
            ->where('ExtRef', $DocEntry)
            ->get();

        if (!$records) {
            return;
        }
        foreach ($records as $record) {
            $record->update([
                'Status' => 3,
            ]);
            if ($record->BaseType) {
                $this->closeOtherDocuments($record->BaseType, $DocEntry);
            }
        }
    }

    public function mobileAppFields()
    {
        $resp = (new FormFieldsService())->getFormFields();
        return response(['formFields' => $resp]);
    }

    /**
     * Get Scan Logs
     */
    public function getScanLogs()
    {
        try {
            $data = GMS1::with('objecttype', 'creator')
                ->orderBy('id', 'desc')
                ->get();

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function doNotReleaseGoods(Request $request, $id)
    {

        try {
            if (!$request['BackUpMode']) {
                $data = OGMS::findOrFail($id);

                GMS1::where('id', $data->ScanLogID)->update([
                    'Status' => 4
                ]);

                $data->update([
                    "Comment" => $request['Comment'],
                    'Status' => 4,
                ]);
            }
            if ($request['BackUpMode'] == 1) {
                $data = BackUpModeLines::where('BaseEntry', $id)->firstOrFail();
                $data->where('id', $id)
                    ->update([
                        'ReleaseStatus' => 2,
                        "Comment" => $request['Comment'],
                    ]);
                return response()
                    ->json(
                        [
                            'message' => "The given data was invalid",
                            'resultCode' => 1500,
                            'BackUpMode' => 1,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'Details' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                        ],
                        200
                    );
            }
            return response()
                ->json(
                    [
                        'message' => "The given data was invalid",
                        'resultCode' => 1500,
                        'BackUpMode' => 0,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'Details' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
                        ],
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
                        ],
                    ],
                    200
                );
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function postScanLogDetails($attachments, $scanLogID)
    {
        try {

            DB::connection("tenant")->beginTransaction();
            foreach ($attachments as $key => $val) {
                GMS2::firstOrCreate([
                    'DocEntry' => $scanLogID,
                    'Type' => $val['type'],
                    'Name' => $val['title'],
                    'Content' => $val['content'] ?? 0,
                ]);
            }

            DB::connection("tenant")->commit();
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function updateScanLogDetails(Request $request, $id)
    {
        try {
            $scan_detail = GMS2::where('id', $id)->update([
                'Content' => $request['value'],
            ]);
            return (new ApiResponseService())->apiSuccessResponseService($scan_detail);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function saveScanLogDetails(Request $request)
    {

        try {
            $scanLog = GMS1::findOrFail($request['ScanLogId']);

            $attachments = $request['attachments'];
            DB::connection("tenant")->beginTransaction();
            foreach ($attachments as $key => $val) {
                GMS2::firstOrCreate([
                    'DocEntry' => $scanLog->id,
                    'Type' => $val['type'],
                    'Name' => $val['title'],
                    'Content' => $val['value'],
                ]);
            }
            DB::connection("tenant")->commit();
            return (new ApiResponseService())->apiSuccessResponseService("Uploaded Successfully");
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function filterScanLogs(Request $request)
    {

        try {
            $query = OGMS::orwhere('Status', 2);
            if ($request->query('date')) {
                $query =   $query->whereDate('updated_at', Carbon::createFromFormat('Y-m-d',  $request->query('date')));
            }
            if ($request->query('ObjType')) {
                $query =   $query->where('ObjType', $request->query('ObjType'));
            }
            if ($request->query('ExtRefDocNum')) {
                $query =   $query->where('ExtRefDocNum', $request->query('ExtRefDocNum'));
            }
            $allData =  $query->get();

            foreach ($allData as $key => $record) {
                $lineDetails = explode('|', $record->LineDetails);
                $itemRows = [];
                foreach ($lineDetails as $key => $value) {
                    $data = explode(';', $value);

                    $item = [
                        'ItemCode' => $data[0],
                        'Quantity' => $data[1],
                    ];

                    array_push($itemRows, $item);
                }

                $record->LineDetails = $itemRows;
            }
            return (new ApiResponseService())->apiSuccessResponseService($allData);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
