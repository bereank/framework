<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Inventory\Services\InventoryService;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Services\AuthorizationService;

class DispatchController extends Controller
{
    public function index()
    {

        $RlpCode = null;
        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        $OBJType = request()->input('ObjType');
        $startDate = request()->filled('StartDate') ? Carbon::parse(request()->input('StartDate'))->startOfDay() : Carbon::now()->startOfDay();
        $endDate = request()->filled('EndDate') ? Carbon::parse(request()->input('EndDate'))->endOfDay() : Carbon::now()->endOfDay();
        $salesEmp = \Request::has('SlpCode') ? explode(",", \Request::get('SlpCode')) : [];
        $driverCode = \Request::has('RlpCode') ? explode(",", \Request::get('RlpCode')) : [];
        $DocStatus =  request()->filled('DocStatus') ? request()->input('DocStatus') : 'O';
        $vehicle_id = \Request::has('vehicle_id') ? explode(",", \Request::get('vehicle_id')) : [];
        $searchItm = \Request::has('search') ? explode(",", \Request::get('search')) : [];
        $CallId =  request()->filled('CallId') ? request()->input('CallId') : false;

        if (!$user->SUPERUSER) {
            $RlpCode = $user->oudg->Driver ?? 0;
        };
        // $ownerData = [];
        // $dataOwnership = (new AuthorizationService())->CheckIfActive($OBJType, $user->EmpID);

        // $ownerData =  (new AuthorizationService())->getDataOwnershipAuth($OBJType, 1);


        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $OBJType)
            ->first();

        try {
            $dispItems = $DocumentTables->ObjectHeaderTable::with([
                'document_lines' => function ($query) use ($DocStatus) {
                    $query->where('LineStatus',  $DocStatus)
                        ->select(
                            'id',
                            'Quantity',
                            'DelivrdQty',
                            'ItemCode',
                            'OpenQty',
                            'DocEntry',
                            'LineNum',
                            'PickStatus',
                            'SlpCode',
                            'RlpCode',
                            'Dscription',
                            'Price',
                            'unitMsr',
                            'VatSum',
                            'LineTotal',
                            'DiscPrcnt',
                            'Rate',
                            'TaxCode',
                            'PriceAfVAT',
                            'PriceBefDi',
                            'DocDate',
                            'LineStatus',
                            'UomCode',
                            'created_at'
                        );
                },
            ])
                ->with(['oslp' => function ($query) {
                    $query->select('SlpCode', 'SlpName', 'Telephone', 'Mobil', 'id');
                }])
                ->with(['driver' => function ($query) {
                    $query->select('RlpCode', 'RlpName', 'Telephone', 'Mobil', 'id');
                }])
                ->with(['vehicle' => function ($query) {
                    $query->select('id', 'RegistrationNO', 'capacity');
                }])
                ->with(['call' => function ($query) {
                    $query->select('*');
                }])
                ->with(['ocrd' => function ($query) {
                    $query->select('id', 'CardCode', 'CardName', 'Address', 'Phone1', 'Phone2');
                }])
                ->with(['attachments.attachment_lines'])
                ->when(!empty($salesEmp), function ($query) use ($salesEmp) {
                    return $query->whereIn('SlpCode', $salesEmp);
                })
                ->when(!empty($driverCode), function ($query) use ($driverCode) {
                    return $query->whereIn('RlpCode', $driverCode);
                })
                ->when(!empty($vehicle_id), function ($query) use ($vehicle_id) {
                    return $query->whereIn('vehicle_id', $vehicle_id);
                })
                ->when($CallId, function ($query) use ($CallId) {
                    return $query->where('ClgCode', $CallId);
                })
                ->with(['ohem' => function ($query) {
                    $query->select(
                        'id',
                        'firstName',
                        'middleName',
                        'lastName',
                        'manager',
                        'empID',
                        'dept',
                        'branch',
                        'CompanyID'
                    );
                }])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    'id',
                    'DocNum',
                    'CardName',
                    'DocNum',
                    'DocDate',
                    'ExtRefDocNum',
                    'CardCode',
                    'NumAtCard',
                    'ClgCode',
                    'DocStatus',
                    'ObjType',
                    'DocType',
                    'ExtRef',
                    'SlpCode',
                    'RlpCode',
                    'vehicle_id',
                    'Comments',
                    'OwnerCode',
                    'AtcEntry',
                    'Attachment',
                )
                ->when(!empty($searchItm), function ($query) use ($searchItm) {
                    return $query->where(function ($query) use ($searchItm) {
                        return      $query->orwhereIn('CardCode', $searchItm)
                            ->orWhere(function ($query) use ($searchItm) {
                                for ($i = 0; $i < count($searchItm); $i++) {
                                    $query->orwhere('CardName', 'like', '%' . $searchItm[$i] . '%');
                                }
                            })
                            ->orWhereIn('ExtRefDocNum', $searchItm);
                    });
                })
                ->when(!$user->SUPERUSER, function ($query) use ($RlpCode) {
                    $query->where('RlpCode',  $RlpCode);
                })
                // ->when($dataOwnership->Active, function ($query) use ($ownerData) {
                //     $query->wherein('OwnerCode', $ownerData);
                // })
                ->where('DocStatus', $DocStatus)->orderBy('id', 'desc')->take(250)->get();


            // foreach ($dispItems as $document) {
            //     $total_Qty = 0;
            //     foreach ($document->document_lines as $document_line) {

            //         $total_Qty += $document_line->Quantity;
            //     }
            //     $document->totalQty = $total_Qty;
            // }

            return (new ApiResponseService())->apiSuccessResponseService($dispItems);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show($id)
    {
        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        $OBJType = request()->input('ObjType');

        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $OBJType)
            ->first();

        try {
            $dispItems = $DocumentTables->ObjectHeaderTable::with([
                'document_lines' => function ($query) {
                    $query->select(
                        'id',
                        'Quantity',
                        'DelivrdQty',
                        'ItemCode',
                        'OpenQty',
                        'DocEntry',
                        'LineNum',
                        'SlpCode',
                        'RlpCode',
                        'Dscription',
                        'DocDate',
                        'LineStatus',
                        'UomCode',
                        'unitMsr',
                        'LineTotal',
                        'ClgCode',
                        'Price',
                        'unitMsr',
                        'VatSum',
                        'LineTotal',
                        'DiscPrcnt',
                        'Rate',
                        'TaxCode',
                        'PriceAfVAT',
                        'PriceBefDi',
                        'OwnerCode',
                        'BaseEntry',
                        'created_at'
                    );
                },
            ])
                ->where('id', $id)
                ->with(['oslp' => function ($query) {
                    $query->select('SlpCode', 'SlpName', 'Telephone', 'Mobil', 'id');
                }])
                ->with(['driver' => function ($query) {
                    $query->select('RlpCode', 'RlpName', 'Telephone', 'Mobil', 'id');
                }])
                ->with(['vehicle' => function ($query) {
                    $query->select('id', 'RegistrationNO', 'capacity');
                }])
                ->with(['call' => function ($query) {
                    $query->select('*');
                }])
                ->with(['ocrd' => function ($query) {
                    $query->select('id', 'CardCode', 'CardName', 'Address', 'Phone1', 'Phone2');
                }])
                ->with(['attachments.attachment_lines'])
                ->with(['ohem' => function ($query) {
                    $query->select(
                        'id',
                        'firstName',
                        'middleName',
                        'lastName',
                        'manager',
                        'empID',
                        'dept',
                        'branch',
                        'CompanyID'
                    );
                }])
                ->select(
                    'id',
                    'DocNum',
                    'CardName',
                    'NumAtCard',
                    'DocNum',
                    'DocDate',
                    'ExtRefDocNum',
                    'CardCode',
                    'DocStatus',
                    'ObjType',
                    'DocType',
                    'ExtRef',
                    'SlpCode',
                    'RlpCode',
                    'vehicle_id',
                    'ClgCode',
                    'OwnerCode',
                    'Comments',
                    'AtcEntry',
                    'Attachment'
                )
                ->first();
            return (new ApiResponseService())->apiSuccessResponseService($dispItems);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function store(Request $request)
    {

        try {
            DB::beginTransaction();
            $this->validate($request, [
                'ObjType' => 'required',
                'items' => 'required|array',
            ]);

            $itemsCollection = collect($request['items']);
            $uniqueCardCodes = $itemsCollection->unique('CardCode');

            foreach ($uniqueCardCodes as $key => $item) {

                $itemRowsData = $itemsCollection->filter(function ($doc, $key) use ($item) {
                    return $doc['CardCode'] == $item['CardCode'];
                });
                $callTime = $request['CallDate'] ??  Carbon::now()->toDateString();

                if ($request['ObjType'] == 212) {
                    // create calls to BP.
                    $user = Auth::user();
                    $OCLG = OCLG::whereDate('CallDate', $callTime)
                        ->where('CardCode', $item['CardCode'])
                        ->where('RlpCode', $item['RlpCode'])
                        ->first();
                    if (is_null($OCLG)) {
                        //return "no call";
                        $OCLG = OCLG::create([
                            'SlpCode' => $item['SlpCode'] ?? null, // Sales Employee
                            'RlpCode' => $item['RlpCode'],
                            'CardCode' => $item['CardCode'], // Oulet/Customer
                            'CallDate' => $item['CallDate'] ??  Carbon::now()->toDateString(), //  Call Date
                            'CallTime' => $item['CallTime'] ?? Carbon::now()->startOfDay(), // CallTime
                            'CallEndTime' => $item['CallEndTime'] ?? Carbon::now()->endOfDay(), // CallTime
                            'Repeat' => $item['Repeat'] ?? "N", // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
                            'UserSign' => $user->id ?? null,
                        ]);
                    }
                }

                $ObjType = $request['ObjType'];
                $TargetTables = APDI::with('pdi1')
                    ->where('ObjectID', $ObjType)
                    ->first();

                if (!$TargetTables) {
                    DB::rollBack();
                    return (new ApiResponseService())->apiFailedResponseService("Not found document with objtype " . $ObjType);
                }

                if ($item['ObjType'] && $item['id']) {
                    $BaseTables = APDI::with('pdi1')
                        ->where('ObjectID', $item['ObjType'])
                        ->first();

                    if (!$BaseTables) {
                        DB::rollBack();
                        return (new ApiResponseService())->apiFailedResponseService("Not found document with base type ");
                    }

                    $baseDocHeader = $BaseTables->ObjectHeaderTable::where('id', $item['id'])
                        ->first();
                    if ($baseDocHeader->DocStatus == "C" && ($item['ObjType'] != 13 ||  $request['ObjType'] != 211)) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Copying to not Possible, Base Document is closed");
                    }
                } else {
                    DB::rollBack();
                    return (new ApiResponseService())->apiFailedResponseService("Not found document with base type ");
                }
                $Numbering = (new DocumentsService())
                    ->getNumSerieByObjectId($request['ObjType']);

                $user = Auth::user();
                $default_vehicle = ORLP::where('RlpCode', $request['RlpCode'])->select('vehicle_id')->first();
                $NewDocDetail = [
                    "UserSign" => $user->id,
                    "DocDate" => Carbon::now()->format('Y-m-d'),
                    "CardCode" => $item['CardCode'],
                    "CardName" => $item['CardName'],
                    "DocNum" => $Numbering['NextNumber'],
                    "ObjType" => $request['ObjType'] ?? null,
                    "DocType" => $request['DocType'] ?? null,
                    "ClgCode" => $request['ObjType'] == 212 ?  $OCLG->id : null,
                    'SlpCode' =>  $item['SlpCode'] ?? null,
                    'RlpCode' =>  $request['RlpCode'] ?? $baseDocHeader->RlpCode ?? null,
                    'vehicle_id' =>  $request['vehicle_id'] ?? $default_vehicle?->vehicle_id,
                    'OwnerCode' => $user->EmpID ?? null,
                    'Comments' => $request['Comments'] ?? null,
                    // 'Attachment' => $request['Attachment'] ?? null,
                    'NumAtCard' => $item['NumAtCard'] ?? null

                ];

                $newDoc = new $TargetTables->ObjectHeaderTable(array_filter($NewDocDetail));

                $newDoc->save();

                foreach ($itemRowsData as $key => $row) {

                    foreach ($row['document_lines'] as $key => $value) {
                        $LineNum = ++$key;
                        $rowdetails = [
                            'DocEntry' => $newDoc->id,
                            'LineNum' => $LineNum,
                            'ItemCode' => $value['ItemCode'] ?? null,
                            'Dscription' => $value['Dscription'] ?? null,
                            'Quantity' => $value['OpenQty'] ?? 1,
                            'BaseQty' => $value['OpenQty'] ?? 1,
                            'PackQty' => $value['PackQty'] ?? null,
                            'Price' => $value['Price'] ?? 0,
                            'UomCode' => $value['UomCode'] ?? null,
                            'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null,
                            'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null,
                            'OwnerCode' => $value['OwnerCode'] ?? null,
                            'GTotal' => $value['GTotal'] ?? 0,
                            'BaseRef' => $value['DocNum'] ?? 0,
                            'BaseEntry' => $value['DocEntry'] ?? 0,
                            'BaseLine' => $value['LineNum'] ?? 0,
                            'OpenQty' => $value['OpenQty'] ?? 0,
                            'BaseType' => $row['ObjType'],
                            'DocDate' =>  Carbon::now()->format('Y-m-d'),
                            'SlpCode' => $value['SlpCode'] ?? null,
                            'RlpCode' =>  $request['RlpCode'] ?? $baseDocHeader->RlpCode ?? null,
                            'vehicle_id' =>  $request['vehicle_id'] ?? $baseDocHeader->vehicle_id ?? null,
                            'DiscPrcnt' => $value['DiscPrcnt'] ?? 0, //    Discount %
                            'Rate' => $value['Rate'] ?? 0,
                            'TaxCode' =>  $value['TaxCode'] ?? null, //    Tax Code
                            'PriceAfVAT' =>  $value['PriceAfVAT'] ?? 0, //
                            'PriceBefDi' =>  $value['PriceBefDi'] ?? 0, // Unit Price
                            'LineTotal' => $value['LineTotal'] ?? 0, //    Total (LC)
                            'WhsCode' => $value['WhsCode'] ?? null, //    Warehouse Code
                            'ShipDate' => $value['ShipDate'] ?? null, //
                            'CodeBars' => $value['CodeBars'] ?? null, //    Bar Code
                            'SerialNum' => $value['SerialNum'] ?? null //    Serial No.
                        ];

                        $rowItems = new $TargetTables->pdi1[0]['ChildTable']($rowdetails);
                        $rowItems->save();
                        (new InventoryService())->dispatchEffectOnOrder(
                            $row['ObjType'],
                            $value['OpenQty'],
                            $value['id']
                        );
                    }

                    if ($item['ObjType'] && $item['id']) {
                        $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $row['id'])
                            ->where('OpenQty', 0)->where('LineStatus', "O")->update([
                                'LineStatus' => "C",
                            ]);
                        if ($BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $row['id'])
                            ->where('OpenQty', '>=', 1)
                            ->doesntExist()
                        ) {
                            $baseDocHeader->update([
                                'DocStatus' => "C",
                            ]);

                            $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $row['id'])->update([
                                'LineStatus' => "C",
                            ]);
                        }
                    }
                }
                (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
            }
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService($newDoc);
        } catch (\Throwable $th) {
            DB::rollBack();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ObjType' => 'required',
        ]);


        $ObjType = $request['ObjType'];


        if ($request['ObjType'] && $id) {
            $DocTables = APDI::with('pdi1')
                ->where('ObjectID', $request['ObjType'])
                ->first();

            if (!$DocTables) {
                DB::rollBack();
                return (new ApiResponseService())->apiFailedResponseService("Not found document with base type ");
            }

            $DocHeader = $DocTables->ObjectHeaderTable::where('id', $id)
                ->first();
        } else {
            DB::rollBack();
            return (new ApiResponseService())->apiFailedResponseService("Not found document  type ");
        }
        $user = Auth::user();
        /**
         * Check If Authorized
         */
        (new AuthorizationService())->checkIfAuthorize($DocTables->id, 'update');

        (new AuthorizationService())->CheckAllowedEdit($ObjType, $DocHeader->OwnerCode);

        $NewDocDetail = [
            "UserSign" => $user->id,
            "DocDate" => Carbon::now()->format('Y-m-d'),
            "CardCode" => $request['CardCode'],
            "CardName" => $request['CardName'],
            "DocNum" => $request['DocNum'],
            "ObjType" => $request['ObjType'] ?? null,
            "DocType" => $request['DocType'] ?? null,
            'SlpCode' =>  $request['SlpCode'] ?? null,
            'RlpCode' =>  $request['RlpCode'] ?? null,
            'vehicle_id' =>  $request['vehicle_id'] ?? null,
            'OwnerCode' => $request['OwnerCode'] ??  "",
            'Comments' => $request['vehicle_id'] ?? null,
            'Attachment' => $request['Attachment'] ?? null
        ];

        $newDoc =  $DocTables->ObjectHeaderTable::where('id', $id)->update(
            $NewDocDetail
        );
        foreach ($request['document_lines'] as $key => $value) {
            $originalQty =    $DocTables->pdi1[0]['ChildTable']::where(
                'id',
                $value['id']
            )->select('Quantity', 'OpenQty')->first();
            $diffQty =  intval($value['Quantity']) - $originalQty->Quantity;

            $rowdetails = [
                'DocEntry' => $value['DocEntry'],
                'LineNum' => $value['LineNum'],
                'ItemCode' => $value['ItemCode'] ?? null,
                'Dscription' => $value['Dscription'] ?? null,
                'Quantity' => $value['Quantity'],
                'PackQty' => $value['PackQty'] ?? null,
                'Price' => $value['Price'] ?? 0,
                'UomCode' => $value['UomCode'] ?? null,
                'unitMsr' => array_key_exists('unitMsr', $value) ? $value['unitMsr'] : null,
                'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null,
                'OwnerCode' => $value['OwnerCode'] ?? null,
                'GTotal' => $value['GTotal'] ?? 0,
                'BaseRef' => $value['DocNum'] ?? 0,
                'BaseEntry' => $value['DocEntry'] ?? 0,
                'BaseLine' => $value['LineNum'] ?? 0,
                'OpenQty' =>  $originalQty->OpenQty  + $diffQty,
                'BaseType' => $request['ObjType'],
                'DocDate' =>  Carbon::now()->format('Y-m-d'),
                'SlpCode' => $value['SlpCode'] ?? null,
                'RlpCode' => $request['RlpCode']  ?? null,
            ];

            $rowItems =  $DocTables->pdi1[0]['ChildTable']::where('id', $value['id'])->update($rowdetails);
        }

        if ($request['ObjType'] && $id) {
            $DocTables->pdi1[0]['ChildTable']::where('DocEntry', $request['id'])
                ->where('OpenQty', 0)->where('LineStatus', "O")->update([
                    'LineStatus' => "C",
                ]);
            if ($DocTables->pdi1[0]['ChildTable']::where('DocEntry', $request['id'])
                ->where('OpenQty', '>=', 1)
                ->doesntExist()
            ) {
                $DocHeader->update([
                    'DocStatus' => "C",
                ]);

                $DocTables->pdi1[0]['ChildTable']::where('DocEntry', $request['id'])->update([
                    'LineStatus' => "C",
                ]);
            } else {
                $DocHeader->update([
                    'DocStatus' => "O",
                ]);

                $DocTables->pdi1[0]['ChildTable']::where('DocEntry', $request['id'])
                    ->where('OpenQty', '>=', 1)
                    ->update([
                        'LineStatus' => "O",
                    ]);
            }
        }

        return (new ApiResponseService())->apiSuccessResponseService(['message' =>
        "Successfully Posted"]);
    }
    public function documentCancellation($ObjType, $id)
    {


        $DocTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$DocTables) {
            return (new ApiResponseService())->apiFailedResponseService("Not found document with base type ");
        }
        /**
         * Check If Authorized
         */
        (new AuthorizationService())->checkIfAuthorize($DocTables->id, 'update');


        $DocHeader = $DocTables->ObjectHeaderTable::where('id', $id)
            ->with('document_lines')
            ->first();
        if (!$DocHeader) {
            return (new ApiResponseService())->apiFailedResponseService("Document Not Found");
        }

        if ($DocHeader->DocStatus == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document is closed");
        }
        if ($DocHeader->CANCELED == "Y") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document already cancelled");
        }
        if ($DocHeader->CANCELED == "C") {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible on a Cancellation Document");
        }
        $DocLines = $DocTables->pdi1[0]['ChildTable']::where('DocEntry', $id)
            ->where(function ($query) {
                $query->orWhereColumn('OpenQty', '!=', 'Quantity')
                    ->orWhere('LineStatus', '!=', 'O');
            })
            ->exists();
        if (!$DocLines) {
            try {

                DB::beginTransaction();
                $Numbering = (new DocumentsService())
                    ->getNumSerieByObjectId($ObjType);
                $cancellation_header =  new $DocTables->ObjectHeaderTable($DocHeader->toArray());
                $cancellation_header->CANCELED = 'C';
                $cancellation_header->DocStatus = 'C';
                $cancellation_header->DocNum = $Numbering['NextNumber'];
                $cancellation_header->BaseType = $DocHeader->ObjType;
                $cancellation_header->BaseEntry = $DocHeader->id;
                $cancellation_header->OwnerCode =   Auth::user()->EmpID ?? "";
                $cancellation_header->save();
                //Close Current document
                $DocTables->ObjectHeaderTable::where('id', $DocHeader->id)
                    ->update([
                        'DocStatus' => 'C'
                    ]);
                foreach ($DocHeader->document_lines as $key => $line) {

                    $LineNum = ++$key;
                    $cancellation_line = new  $DocTables->pdi1[0]['ChildTable']($line->toArray());
                    $cancellation_line->DocEntry = $cancellation_header->id;
                    $cancellation_line->BaseType = $DocHeader->ObjType;
                    $cancellation_line->BaseEntry = $DocHeader->id;
                    $cancellation_line->BaseLine = $line->LineNum;
                    $cancellation_line->LineNum =  $LineNum;
                    $cancellation_line->LineStatus = "C";
                    $cancellation_line->save();
                    $BaseTables = APDI::with('pdi1')
                        ->where('ObjectID', $line->BaseType)
                        ->first();
                    //Open base document header
                    $baseHeader =   $BaseTables->ObjectHeaderTable::where('id', $line->BaseEntry)
                        ->update([
                            'DocStatus' => "O"
                        ]);


                    $baseLine = $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $line->BaseEntry)
                        ->where('LineNum', $line->BaseLine)->first();
                    //Open base line
                    $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $line->BaseEntry)
                        ->where('LineNum', $line->BaseLine)
                        ->update([
                            'LineStatus' => "O",
                            'OpenQty' => $line->OpenQty + $baseLine->OpenQty,
                        ]);
                    Log::info([$line->OpenQty, "BASE" => $baseLine->OpenQty]);
                    //Close current line
                    $DocTables->pdi1[0]['ChildTable']::where('id', $line->id)
                        ->update([
                            'LineStatus' => "C",
                        ]);
                }
                (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
                DB::commit();
                return (new ApiResponseService())->apiSuccessResponseService(['message' =>
                "Successfully Canceled"]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
            }
        } else {
            return (new ApiResponseService())
                ->apiFailedResponseService("Operation not Possible, Document Lines Not Open");
        }
    }
    public function getSummaries()
    {
        $RlpCode = null;
        $user = User::where('id', Auth::user()->id)->with('oudg')->first();
        $startDate = request()->filled('StartDate') ? Carbon::parse(request()->input('StartDate'))->startOfDay() : Carbon::now()->startOfDay();
        $endDate = request()->filled('EndDate') ? Carbon::parse(request()->input('EndDate'))->endOfDay() : Carbon::now()->endOfDay();
        $salesEmp = \Request::has('SlpCode') ? explode(",", \Request::get('SlpCode')) : [];
        $driverCode = \Request::has('RlpCode') ? explode(",", \Request::get('RlpCode')) : [];
        $DocStatus =  request()->filled('DocStatus') ? request()->input('DocStatus') : 'O';
        $vehicle_id = \Request::has('vehicle_id') ? explode(",", \Request::get('vehicle_id')) : [];
        $CallId =  request()->filled('CallId') ? request()->input('CallId') : false;

        $objects = [13, 211, 212, 213, 214];

        $summaries = [];
        foreach ($objects as $OBJType) {
            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', $OBJType)
                ->first();
            $doc_status = ['Open' => 'O', 'Closed' => 'C'];
            $status = [];
            foreach ($doc_status as $key => $DocStatus) {

                $dispItems = $DocumentTables->ObjectHeaderTable::with([
                    'document_lines' => function ($query) use ($DocStatus) {
                        $query->where('LineStatus',  $DocStatus);
                    },
                ])
                    ->when(!empty($salesEmp), function ($query) use ($salesEmp) {
                        return $query->whereIn('SlpCode', $salesEmp);
                    })
                    ->when(!empty($driverCode), function ($query) use ($driverCode) {
                        return $query->whereIn('RlpCode', $driverCode);
                    })
                    ->when(!empty($vehicle_id), function ($query) use ($vehicle_id) {
                        return $query->whereIn('vehicle_id', $vehicle_id);
                    })
                    ->when($CallId, function ($query) use ($CallId) {
                        return $query->where('ClgCode', $CallId);
                    })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->when(!$user->SUPERUSER, function ($query) use ($RlpCode) {
                        $query->where('RlpCode',  $RlpCode);
                    })
                    ->where('DocStatus', $DocStatus)->orderBy('id', 'desc')->count();

                $status[$key] = $dispItems;
            }

            $status['Document'] =  $DocumentTables->DocumentName;
            array_push($summaries, $status);
        }
        return $summaries;
    }
}
