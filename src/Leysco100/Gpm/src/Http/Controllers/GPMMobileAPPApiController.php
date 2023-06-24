<?php

namespace Leysco\Gpm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Leysco\Gpm\Jobs\SendEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\FormField;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Marketing\Models\GMS1;
use Leysco100\Shared\Models\Marketing\Models\OGMS;
use Leysco100\Shared\Models\Administration\Models\GMS2;
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
        DB::beginTransaction();
        try {
            $fullDocNum = explode("-", $request['DocNum']);

            if (count($fullDocNum) < 3) {
                return response()
                    ->json(
                        [
                            'message' => "The given data was invalid",
                            'resultCode' => 1500,
                            'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                            'errors' => [
                                'record' => 'Discrepancy Noted- Don’t Release Goods',
                            ],
                        ],
                        200
                    );
            }
            $originSystem = $fullDocNum[0];
            $ObjTypeString = $fullDocNum[1];
            $DocNum = $fullDocNum[2];

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

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            Log::info($th);
            return response()
                ->json(
                    [
                        'message' => "The given data was invalid",
                        'resultCode' => 1500,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
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

        //Document Doest Not Exist
        if (!$record) {
            $newRecord->update([
                'Status' => 1,
            ]);

            dispatch(new SendEmailJob($emails, $newRecord->id));
            // Mail::to($emails)->send(new GPMNotificationMail($newRecord->id));

            return response()
                ->json(
                    [
                        'message' => "Document Doest Not Exist",
                        'resultCode' => 1500,
                        'ScanLogId' => $newRecord->id,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
                        ],
                    ],
                    200
                );
        }



        if ($record->BaseType && $record->BaseEntry) {

            /**
             * Check The Base Document is closed
             */
            $baseRecord = OGMS::where('ObjType', $record->BaseType)
                ->where('ExtRef', $record->BaseEntry)
                ->first();

            if ($baseRecord && $ObjType != 'DISPNOT') {
                if ($baseRecord->Status != 0) {
                    $this->closeOtherDocuments($record->BaseType, $record->BaseEntry);

                    $record->update([
                        'Status' => 2,
                        'ScanLogID' => $newRecord->id,
                    ]);


                    $newRecord->update([
                        'Status' => 2,
                        'Comment' => "Base Document Closed",
                    ]);


                    return response()
                        ->json(
                            [
                                'message' => "There exist scanned base document",
                                'resultCode' => 1500,
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
        }

        /**
         * status reference
         * 3=Released,2=Scanned But Flagged,1=Scanned But Not Confirmed, 0=Open
         */

        //If the document is not in Open State or Not Confirmed
        if (($record->Status != 0) && ($record->Status != 1)) {
            $newRecord->update([
                'Status' => 2,
                'ScanLogID' => $record->id,
            ]);

            // Mail::to($emails)->send(new GPMNotificationMail($newRecord->id));
            // email notification as a job
            dispatch(new SendEmailJob($emails, $newRecord->id));
            return response()
                ->json(
                    [
                        'message' => "Duplicate Scan",
                        'resultCode' => 1500,
                        'ScanLogId' => $newRecord->id,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
                        'errors' => [
                            'record' => 'Discrepancy Noted- Don’t Release Goods',
                        ],
                    ],
                    200
                );
        }

        $newRecord->update([
            'Status' => 0,
            'DocID' => $record->id,
        ]);

        //Close SubSequent Documents
        if ($record->BaseType && $record->BaseEntry) {
            $this->closeOtherDocuments($record->BaseType, $record->BaseEntry);
        }

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
                    'resultDesc' => 'Kindly confirm the Documen',
                    'errors' => [
                        'record' => "",
                        "DocumentDetails" => $singleDocument,
                    ],
                ],
                200
            );
    }

    public function update($id)
    {

        try {
            $data = OGMS::findOrFail($id);

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
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function closeOtherDocuments($ObjType, $DocEntry)
    {
        $record = OGMS::where('ObjType', $ObjType)
            ->where('ExtRef', $DocEntry)
            ->first();

        if (!$record) {
            return;
        }
        $record->update([
            'Status' => 1,
        ]);

        if ($record->BaseType) {
            $this->closeOtherDocuments($record->BaseType, $DocEntry);
        }
    }

    public function mobileAppFields()
    {

        //         $data = [
        //
        //             [
        //                 "key" => "Name",
        //                 "indexno" => "2",
        //                 "title" => "Enter Customer Name/Weka Jina La Mteja",
        //                 "type" => "Text",
        //                 "Mandatory" => "Y",
        //             ],
        ////             [
        ////                 "key" => "phone",
        ////                 "indexno" => "2",
        ////                 "title" => "Enter Customer Phone/Weka nambari ya simu ya Mteja",
        ////                 "type" => "Phone",
        ////                 "Mandatory" => "Y",
        ////             ],
        //
        //             [
        //                 "key" => "FormOfIdentity",
        //                 "indexno" => "1",
        //                 "title" => "Enter Form Of Identity/Weka Aina ya Kitambulisho",
        //                 "type" => "Dropdown",
        //                 "Mandatory" => "Y",
        //                 "values" => [
        //                     [
        //                         "id" => 1,
        //                         "Name" => "National Id",
        //                     ],
        //                     [
        //                         "id" => 2,
        //                         "Name" => "Driving Licence",
        //                     ],
        //                     [
        //                         "id" => 3,
        //                         "Name" => "Passport",
        //                     ],
        //                 ],
        //             ],
        //             [
        //                 "key" => "IdentityNo",
        //                 "indexno" => "3",
        //                 "title" => "Enter Customer Identity No/Weka Namba ya Kitambulisho ya Mteja",
        //                 "type" => "Text",
        //                 "Mandatory" => "Y",
        //             ],
        //             [
        //                 "key" => "IdentityDocumentPhotoOne",
        //                 "indexno" => "4",
        //                 "title" => "Take a Picture Of The Identity Document (Photo 1)/Chukua Picha ya Kitambulisho",
        //                 "type" => "Photo",
        //                 "Mandatory" => "Y",
        //             ],
        //             [
        //                 "key" => "IdentityDocumentPhotoTwo",
        //                 "indexno" => "5",
        //                 "title" => "Take a Picture Of The Identity Document (Photo 2)/Chukua Picha ya Kitambulisho",
        //                 "type" => "Photo",
        //                 "Mandatory" => "N",
        //             ],
        //             [
        //                 "key" => "CustomerPhoto",
        //                 "indexno" => "9",
        //                 "title" => "Take Customer Photo/Chukua Picha ya Mteja",
        //                 "type" => "Photo",
        //                 "Mandatory" => "Y",
        //             ],
        //             [
        //                 "key" => "DocumenPhoto",
        //                 "indexno" => "6",
        //                 "title" => "Take a Picture of Exit Document/Chukua Picha ya Delivery Note",
        //                 "type" => "Photo",
        //                 "Mandatory" => "Y",
        //             ],
        //             [
        //                 "key" => "VehicleRegistrationPlate",
        //                 "indexno" => "7",
        //                 "title" => "Vehicle Registration Plate/Chukua Picha ya Plate Namba",
        //                 "type" => "Photo",
        //                 "Mandatory" => "Y",
        //             ],
        //             [
        //                 "key" => "QRCode",
        //                 "indexno" => "8",
        //                 "title" => "Scan the QR Code/Scan na Kutuma",
        //                 "type" => "QRCode",
        //                 "Mandatory" => "Y",
        //             ],
        //         ];


        // $data = [
        //     [
        //         "key" => "Name",
        //         "indexno" => "2",
        //         "title" => "Enter Customer Name",
        //         "type" => "Text",
        //         "Mandatory" => "Y",
        //     ],
        //     [
        //         "key" => "FormOfIdentity",
        //         "indexno" => "1",
        //         "title" => "Enter Form Of Identity",
        //         "type" => "Dropdown",
        //         "Mandatory" => "Y",
        //         "values" => [
        //             [
        //                 "id" => 1,
        //                 "Name" => "National Id",
        //             ],
        //             [
        //                 "id" => 2,
        //                 "Name" => "Driving Licence",
        //             ],
        //             [
        //                 "id" => 3,
        //                 "Name" => "Passport",
        //             ],
        //         ],
        //     ],
        //     [
        //         "key" => "IdentityNo",
        //         "indexno" => "3",
        //         "title" => "Enter Customer Identity No",
        //         "type" => "Text",
        //         "Mandatory" => "Y",
        //     ],
        //     [
        //         "key" => "DeliveryPhoto",
        //         "indexno" => "6",
        //         "title" => "Take a Picture of Delivery",
        //         "type" => "Photo",
        //         "Mandatory" => "Y",
        //     ],
        //     [
        //         "key" => "InvoicePhoto",
        //         "indexno" => "6",
        //         "title" => "Take a Picture of Invoice",
        //         "type" => "Photo",
        //         "Mandatory" => "Y",
        //     ],
        //     [
        //         "key" => "QRCode",
        //         "indexno" => "8",
        //         "title" => "Scan the QR Code/Scan na Kutuma",
        //         "type" => "QRCode",
        //         "Mandatory" => "Y",
        //     ],
        // ];


        // $data = [
        //     [
        //         "key" => "QRCode",
        //         "indexno" => "8",
        //         "title" => "Scan the QR Code/Scan na Kutuma",
        //         "type" => "QRCode",
        //         "Mandatory" => "Y",
        //     ],
        // ];
        // return response([
        //     'formFields' => $data,
        // ]);

        $formFields = FormField::with(['type', 'dropDownValues'])->where('status', 1)->get();
        $data = [];
        foreach ($formFields as $formField) {
            $values = [];
            $field = [
                "key" => $formField->key,
                "indexno" => $formField->indexno,
                "title" => $formField->title,
                "type" => $formField->type->Name,
                "Mandatory" => $formField->mandatory,
            ];

            if (count($formField['dropDownValues']) > 0) {
                foreach ($formField['dropDownValues'] as $value) {
                    $values[] = [
                        'id' => $value['id'],
                        'Name' => $value['Value'],
                    ];
                }
                $field['values'] = $values;
            }
            $data[] = $field;
        }

        return response(['formFields' => $data]);
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
            $data = OGMS::findOrFail($id);

            $data = OGMS::findOrFail($id);

            $data->update([
                "Comment" => $request['Comment'],
                'Status' => 2,
            ]);
            return response()
                ->json(
                    [
                        'message' => "The given data was invalid",
                        'resultCode' => 1500,
                        'resultDesc' => 'Discrepancy Noted- Don’t Release Goods',
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

    public function saveScanLogDetails(Request $request)
    {
        try {
            $scanLog = OGMS::findOrFail($request['ScanLogId']);

            $attachments = $request['attachments'];
            DB::beginTransaction();
            foreach ($attachments as $key => $val) {
                GMS2::firstOrCreate([
                    'DocEntry' => $scanLog->id,
                    'Type' => $val['type'],
                    'Name' => $val['title'],
                    'Content' => $val['value'],
                ]);
            }
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService("Uploaded Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
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
