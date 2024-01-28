<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Leysco100\Shared\Models\OINS;
use Leysco100\Shared\Models\OSCL;
use Leysco100\Shared\Models\OSCO;
use Leysco100\Shared\Models\OSLT;
use Leysco100\Shared\Models\SCL4;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Leysco100\Shared\Models\Gpm\Models\OGMS;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\EODC;
use Leysco100\Shared\Models\Banking\Models\OPDF;
use Leysco100\Shared\Models\Banking\Models\ORCT;
use Leysco100\Shared\Models\Banking\Models\PDF2;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Services\UserFieldsService;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\MarketingDocuments\Models\DRF1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OATS;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Leysco100\Shared\Models\MarketingDocuments\Models\OWDD;
use Leysco100\Shared\Models\MarketingDocuments\Models\WDD1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Actions\TransactionInventoryEffectAction;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OSRN;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Leysco100\Shared\Models\Shared\Services\ServiceCallService;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentService;


class ITransactionController extends Controller
{
    public function getTransactions($ObjType)
    {

        $updated_at = \Request::get('updated_at');
        $docEntry = \Request::get('docEntry');

        if ($ObjType == 1470000113) {
            $ObjType = 205;
        }

        if ($ObjType == 1250000001) {
            $ObjType = 66;
        }

        $isDraft = 0;
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if ($DocumentTables->hasExtApproval == 1) {
            $isDraft = 1;
            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', 112)
                ->first();
        }


        try {
            $documents = $DocumentTables->ObjectHeaderTable::whereNull('ExtRef')
                ->where('ObjType', $ObjType)
                //  ->where('Transfered', 'N')
                ->where(function ($q) use ($docEntry) {
                    if ($docEntry != null) {
                        $q->where('id', $docEntry);
                    }
                })
                ->take(5)
                ->get();

            if ($ObjType == 205) {
                $ObjType = 1470000113;
            }

            if ($ObjType == 66) {
                $ObjType = 1250000001;
            }
            foreach ($documents as $key => $headerVal) {
                /**
                 * Mark The document not transferred
                 */
                // $headerVal->update([
                //     'Transfered' => "Y",
                // ]);

                $headerVal->ExtDocTotal = 0;
                $headerVal->ObjType = $ObjType;
                $headerVal->isDraft = $isDraft;
                $headerVal->DocNum = (int) $headerVal->DocNum;
                $headerVal->DiscPrcnt = 0;
                //Mappding Base Dcoument
                if ($headerVal->BaseEntry && $headerVal->BaseType) {
                    $baseDocDocumentTables = APDI::with('pdi1')
                        ->where('ObjectID', $headerVal->BaseType)
                        ->first();
                    $baseDocument = $baseDocDocumentTables->ObjectHeaderTable::where('id', $headerVal->BaseEntry)->first();
                    //                    if (!$baseDocument->ExtRef) {
                    //                        $documents->forget($key);
                    //                    }
                    $headerVal->BaseEntry = (int) $baseDocument->ExtRef;
                    $headerVal->BaseType = (int) $headerVal->BaseType != 66 ? $headerVal->BaseType : 1250000001;
                }

                if ($headerVal->ReqType == 12) {
                    $headerVal->Requester = User::where('id', $headerVal->Requester)->value('account');
                }
                if ($headerVal->ReqType == 171) {
                    $headerVal->Requester = OHEM::where('id', $headerVal->Requester)->value('empID');
                }

                //Mapping Branch
                $branch = OBPL::where('BPLId', $headerVal->BPLId)->first();
                $headerVal->Branch = $headerVal->BPLId;
                $headerVal->Location = $branch ? $branch->location?->Location : null;
                //Mapping Numbering Series
                $nnm1 = NNM1::where('id', $headerVal->Series)->first();
                $headerVal->Series = $nnm1 ? $nnm1->ExtRef : null;

                //get Attachments
                $headerVal->attachments = $this->getDocumentAttachments($ObjType, $headerVal->id);

                //Custom Udfs

                // $userFields = (object)[
                //     "U_CashMail" => $headerVal->U_CashMail,
                //     "U_ControlCode" => $headerVal->U_ControlCode,
                //     "U_RelatedInv" => $headerVal->U_RelatedInv,
                //     "U_CUInvoiceNum" => $headerVal->U_CUInvoiceNum,
                //     "U_QRCode" => $headerVal->U_QRCode,
                //     "U_QrLocation" => $headerVal->U_QrLocation,
                //     "U_ReceiptNo" => $headerVal->U_ReceiptNo,
                //     "U_CommitedTime" => $headerVal->U_CommitedTime,
                //     "U_IncoTerms" => $headerVal->U_IncoTerms,
                //     "U_PCash" => $headerVal->U_PCash,
                //     "U_Approval"=>"Pending"
                // ];

                //Return UDF's Dynamically

                $data =  APDI::with('pdi1')->where('ObjectID', $ObjType)->first();
                $data['doctype'] = $ObjType;
                if ($data) {
                    $record = (new UserFieldsService())->processUDF($data);
                }
                $userFields = (object)[];
                if ($record) {
                    foreach ($record['HeaderUserFields'] as $headerField) {
                        $userFields->{$headerField['FieldName']} = $headerVal->{$headerField['FieldName']};
                    }

                    $headerVal->UserFields = $userFields;
                }
                $rowData = $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $headerVal->id)->get();
                Log::info("TOTAL LINES FOUND : " . $rowData->count());
                foreach ($rowData as $key => $val) {
                    $val->DiscPrcnt = $val->DiscPrcnt ?? 0;
                    $SerialNumbers = SRI1::where('LineNum', $val->id)
                        ->where(function ($q) use ($isDraft, $headerVal) {
                            if ($isDraft == 1) {
                                $q->where('BaseType', 112);
                            }
                            if ($isDraft == 0) {
                                $q->where('BaseType', $headerVal->ObjType);
                            }
                        })
                        ->where('BaseEntry', $headerVal->id)
                        ->get();
                    foreach ($SerialNumbers as $key => $serial) {
                        $serial->SysNumber = $serial->SysSerial;
                        $serial->DistNumber = OSRN::where('SysNumber', $serial->SysSerial)->where('ItemCode', $serial->ItemCode)->value('DistNumber');
                    }
                    $val->SerialNumbers = $SerialNumbers;
                    if ($val->BaseEntry != null) {
                        $baseDocDocumentTables = APDI::with('pdi1')
                            ->where('ObjectID', $val->BaseType)
                            ->first();
                        $baseDocumentRow = $baseDocDocumentTables->ObjectHeaderTable::where('id', $val->BaseEntry)->first();
                        $val->BaseEntry = (int) $baseDocumentRow->ExtRef;
                        $val->BaseType = (int) $val->BaseType != 66 ? $val->BaseType : 1250000001;
                    }

                    $val->UnitPrice = $val->PriceBefDi;
                    // $val->Price = 0;
                    // $val->UnitPrice = 0;
                    //                    $val->DiscPrcnt = $val->DiscPrcnt;
                    $taxGroup = TaxGroup::where('category', 'O')->where("code", $val->TaxCode)->first();
                    if ($ObjType == "205") {
                        $taxGroup = TaxGroup::where('category', 'I')->where("code", $val->TaxCode)->first();
                    }
                    $val->VatPrcnt = $taxGroup?->rate;
                    $val->VatGroup = $val->TaxCode;
                    $val->UoMEntry = OUOM::Where('id', $val->UomCode)->value('ExtRef') ?? null;

                    // Return Udf's
                    // $val->UserFields = (object)[
                    //     "U_HSCode" => null
                    // ];
                    // Return Udf's
                    $UserFields = (object)[];
                    if ($record) {
                        foreach ($record['LineUserFields'] as $lineField) {
                            $UserFields->{$lineField['FieldName']} = $val->{$lineField['FieldName']};
                        }

                        $val->UserFields = $UserFields;
                    }
                }
                $headerVal->document_lines = $rowData;

                $headerVal->deals = $rowData;

                if ($headerVal->ObjType == 13) {
                    $headerVal->payments = [(new BankingDocumentService())->getInvoicePayment($headerVal->id)];
                }
            }


            Log::info("  ********************************************* " . now() . "********************************************");
            // Log::info($documents);
            return $documents;
            //    Log::info("  ********************************************* " . now() . "********************************************");

        } catch (\Throwable $th) {

            Log::error($th);
            throw $th;
        }
    }

    /**
     * Fetch Attachments
     */

    public static function getDocumentAttachments(int $ObjType, int $DocEntry)
    {
        $oats = OATS::where('DocEntry', $DocEntry)
            ->where('ObjType', 112)
            ->get();
        foreach ($oats as $key => $file) {
            $file->DownloadUrl = asset($file->Path);
        }
        return $oats;
    }

    /**
     * Update Transaction
     *
     */
    public function updateTransactions(Request $request, $ObjType, $DocEntry)
    {

        if ($ObjType == 1470000113) {
            $ObjType = 205;
        }

        if ($ObjType == 1250000001) {
            $ObjType = 66;
        }

        DB::connection('tenant')->beginTransaction();
        try {
            $DocumentTables = APDI::with('pdi1')
                ->where('ObjectID', $ObjType)
                ->first();

            $record = $DocumentTables->ObjectHeaderTable::where('id', $DocEntry)
                ->first();

            $convertDraftToDoc = true;

            if ($ObjType == 191) {
                $convertDraftToDoc = false;
            }

            if ($ObjType == 176) {
                $convertDraftToDoc = false;
            }
            if ($convertDraftToDoc) {
                if ($ObjType != 112 && $request['isDraft'] == 1) {
                    $originalDocumentCreated = $this->convertDraftToDocument($DocEntry);
                    $record = $originalDocumentCreated;
                }
            }

            if (!$record) {
                return response()
                    ->json([
                        'message' => "Document Not Found",
                    ], 500);
            }

            if (!$convertDraftToDoc && $record->ExtRef) {
                $record->update([
                    'Transfered' => "Y",
                ]);

                DB::connection('tenant')->commit();
                return $record;
            }

            $record->update([
                'Transfered' => "Y",
                'ExtRef' => $request['ExtRef'],
                'ExtRefDocNum' => $request['ExtRefDocNum'],
                'ExtDocTotal' => $request['ExtDocTotal'],
            ]);

            if (isset($request["UserFields"])) {
                $userFields = null;
                foreach ($request["UserFields"] as $key => $field) {
                    $userFields[$key] = $field;
                }
                $record->update($userFields);
            }

            if ($ObjType == 205) {
                $record->ObjType = 1470000113;
            }
            if ($ObjType == 66) {
                $record->ObjType = 1250000001;
            }

            if ($ObjType != 112) {
                (new TransactionInventoryEffectAction())->transactionInventoryEffect($ObjType, $record->id);
            }

            DB::connection('tenant')->commit();
            //Sending Sms
            if ($ObjType == 17) {
                (new DocumentsService())->sendingSMS($record->id);
            }

            return $record;
        } catch (\Throwable $th) {

            Log::error($th);
            DB::connection('tenant')->rollback();
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }

    /**
     * Convering Draft to Document
     */
    public function convertDraftToDocument($draftKey)
    {
        $draftDetailsHeader = ODRF::where('id', $draftKey)
            ->first();

        /**
         * Original Draft Doc Number
         */
        $originalDocNum = $draftDetailsHeader->DocNum;

        $draftDetailsRows = DRF1::where('DocEntry', $draftKey)->get();

        $targetTables = APDI::with('pdi1')
            ->where('ObjectID', $draftDetailsHeader->ObjType)
            ->first();

        //Getting New Document Number for the document
        $DocNum = (new DocumentsService())
            ->documentNumberingService(
                $draftDetailsHeader->DocNum,
                $draftDetailsHeader->Series
            );

        //Assigning Document Draft Key and DocNum
        $draftDetailsHeader->DocNum = $DocNum;
        $draftDetailsHeader->draftKey = $draftKey;

        $newDoc = new $targetTables->ObjectHeaderTable($draftDetailsHeader->toArray());
        $newDoc->save();

        foreach ($draftDetailsRows as $key => $row) {

            $row->DocEntry = $newDoc->id;
            $rowItems = new $targetTables->pdi1[0]['ChildTable']($row->toArray());
            $rowItems->save();

            $serialNumbers = (new GeneralDocumentService())->getDocumentLinesSerialNumbers(112, $draftKey, $row->id);

            foreach ($serialNumbers as $key => $serial) {
                $serial->update([
                    "LineNum" => $rowItems->id,
                    "BaseType" => $draftDetailsHeader->ObjType,
                    "BaseEntry" => $newDoc->id,
                ]);
            }
        }

        NumberingSeries::dispatch($draftDetailsHeader->Series);
        /***
         * Update Draft with DocNum Since there was no draft in SAP
         */

        //        $oats = ATCS::where('DocEntry', $draftKey)->where('ObjType', 112)->get();
        //        foreach ($oats as $key => $value) {
        //            $value->update([
        //                'ObjType' => $draftDetailsHeader->ObjType,
        //                'DocEntry' => $newDoc->id,
        //            ]);
        //        }

        //convert payment
        //        $ivoicePaymentDetails = RCT2::where('DocEntry', $draftKey)
        //            ->where('InvoiceId', $originalDocNum)->get();
        //
        //        foreach ($ivoicePaymentDetails as $key => $value) {
        //            $value->update([
        //                'InvoiceId' => $newDoc->DocNum,
        //                'DocEntry' => $newDoc->id,
        //            ]);
        //        }

        $draftInvoicePaymentDetails = PDF2::where("InvoiceDraftKey", $draftDetailsHeader->id)
            ->get();
        if (count($draftInvoicePaymentDetails) > 0) {
            $numberingDetails = (new CommonService())->gettingObjectNumberingSeries(24);

            $draftPayment = OPDF::where("id", $draftInvoicePaymentDetails[0]->DocNum)
                ->first();

            $draftPayment->DocNum = $numberingDetails['DocNum'];
            $draftPayment->Series = $numberingDetails['Series'];
            $payment = new ORCT($draftPayment->toArray());
            $payment->save();

            foreach ($draftInvoicePaymentDetails as $key => $value) {
                $value->InvoiceId = $key;
                $value->DocEntry = $newDoc->id;
                $value->DocNum = $payment->id;
                $invoicePaymentDetails = new RCT2($value->toArray());
                $invoicePaymentDetails->save();
            }
        }


        $draftDetailsHeader->update([
            'DocStatus' => "C",
            'Transfered' => "Y",
            'ExtRef' => "Converted Leysco DocEnry:" . $newDoc->id,
        ]);

        $data = (new ServiceCallService())->updateServiceCallExpenseDetails($newDoc->ObjType, $draftKey, $newDoc->id);

        return $newDoc;
    }
    public function postTransactionErrorLog(Request $request, $ObjType)
    {
        if ($ObjType == 1470000113) {
            $ObjType = 205;
        }

        if ($ObjType == 1250000001) {
            $ObjType = 66;
        }
        $data = $request['data'];
        foreach ($data as $key => $val) {
            $serviceCallOrEquipmentCard = false;

            if ($ObjType == 191) {
                $serviceCallOrEquipmentCard = true;
            }

            if ($ObjType == 176) {
                $serviceCallOrEquipmentCard = true;
            }

            if ($serviceCallOrEquipmentCard) {
                $data = EOTS::updateOrCreate([
                    'DocEntry' => $val['id'],
                    'ObjType' => $ObjType,
                ], [
                    'ErrorMessage' => $val['errorText'],
                ]);

                $DocumentTables = APDI::with('pdi1')
                    ->where('ObjectID', $ObjType)
                    ->first();
                $record = $DocumentTables->ObjectHeaderTable::where('id', $val['id'])
                    ->first();
                $record->update([
                    'Transfered' => "N",
                ]);
            }

            if (!$serviceCallOrEquipmentCard) {
                $data = EOTS::updateOrCreate([
                    'DocEntry' => $val['id'],
                    'ObjType' => $val['isDraft'] == 1 ? 112 : $ObjType,
                ], [
                    'ErrorMessage' => $val['errorText'],
                ]);

                $documentObjectType = $val['isDraft'] == 1 ? 112 : $ObjType;
                /**
                 * Search Document And Mark Synced
                 */
                $DocumentTables = APDI::with('pdi1')
                    ->where('ObjectID', $documentObjectType)
                    ->first();
                $record = $DocumentTables->ObjectHeaderTable::where('id', $val['id'])
                    ->first();
                $record->update([
                    'Transfered' => "N",
                ]);
            }
        }
    }

    /**
     * Add Approver Details
     */
    public function addApprovalDetails(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $val) {
            $owdd = OWDD::updateOrCreate(
                [
                    'WddCode' => $val['WddCode'],
                ],
                [
                    'DocEntry' => $val['DocEntry'],
                    'ObjType' => $val['ObjType'],
                    'DocDate' => $val['DocDate'],
                    'DraftEntry' => $val['DraftEntry'],
                    'Remarks' => $val['Remarks'],
                ]
            );

            $wdd1 = WDD1::updateOrCreate([
                'WddCode' => $val['WddCode'],
                'StepCode' => $val['StepCode'],
                'UserID' => $val['UserID'],
            ], [
                'Status' => $val['Status'],
                'SortId' => $val['SortId'],
                'UpdateDate' => $val['UpdateDate'],
                'Remarks' => $val['Remarks'],
            ]);
        }
    }

    /**
     * Opening Balance Search Document
     */

    public function searchOpeningBalanceTransaction(Request $request, $ObjType)
    {
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $ExtRef = \Request::get('ExtRef');
        $ExtRefDocNum = \Request::get('ExtRefDocNum');
        $data = $DocumentTables->ObjectHeaderTable::where('ExtRef', $ExtRef)
            ->where('ExtRefDocNum', $ExtRefDocNum)
            ->first();
        if (!$data) {
            return null;
        }
        $data->document_lines = $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $data->id)->get();
        $data->UserSign = 1;

        return $data;
    }

    /**
     * Opening Balance Create Document
     */

    public function createOpeningBalanceTransaction(Request $request)
    {
        $user = Auth::user();
        $ObjType = $request['ObjType'];

        if ($ObjType == 1470000113) {
            $ObjType = 205;
        }

        if ($ObjType == 1250000001) {
            $ObjType = 66;
        }
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $request['ObjType'])
            ->first();
        Log::info("CREATING OBJECT: " . $request['ObjType']);
        if (!$DocumentTables) {
            Log::info("Document object does not exist: " . $request['ObjType']);

            abort(500, 'Document object does not exist.');
        }

        $ExtRef = \Request::get('ExtRef');
        if ($ExtRef) {
            $data = $DocumentTables->ObjectHeaderTable::where('ExtRef', $ExtRef)
                ->first();

            if ($data) {
                $data->document_lines = $DocumentTables->pdi1[0]['ChildTable']::where('DocEntry', $data->id)->get();
                $data->UserSign = 1;
                return $data;
            }
        }

        $businessPartner = [];
        if ($ObjType != 205 && $ObjType != 67 && $ObjType != 66 && $ObjType != 1250000001) {

            $businessPartner = OCRD::where('CardCode', $request['CardCode'])->first();

            if (!$businessPartner) {
                Storage::append('IntegratorErrorLog.txt', "Customer Does not Exist:" . $request['DocNum']);

                return response()
                    ->json([
                        'message' => "Customer Doest not exist",
                    ], 422);
            }
        }

        // $nnm1 = NNM1::where('ExtRef', $request['Series'])->first();
        // if (!$nnm1) {
        //     return response()
        //         ->json([
        //             'message' => "Numbering Series Doest not Exist",
        //         ], 422);
        // }

        // $openingBalanceNumbering = (new DocumentsService())
        //     ->getOpeningBalanceNumberingSeries($DocumentTables->id);
        // $DocNum = (new DocumentsService())
        //     ->gettingNumberingSeries($request['ObjType']);
        $Numbering = (new DocumentsService())
            ->getNumSerieByObjectId($ObjType);

        DB::connection("tenant")->beginTransaction();
        try {
            $NewDocDetails = [
                'ObjType' => $request['ObjType'],
                'DocType' => $request['DocType'],
                'DocNum' => $Numbering['NextNumber'],
                'Series' => $Numbering['id'] ?? null,
                'CardCode' => $request['CardCode'] ?? null,
                'Requester' => $request['Requester'] ?? null,
                'ReqType' => $request['ReqType'] ?? null,
                'Department' => $request['Department'] ?? null,
                'DocStatus' => $request['DocStatus'] ?? null,
                'CANCELED' => $request['CANCELED'] ?? null,
                'CardName' => $businessPartner ? $businessPartner->CardName : null,
                'SlpCode' => $request['SlpCode'] ?? null, // Sales Employee
                'U_SalePipe' => $request['U_SalePipe'] ?? null, // Sales Pipe Line
                'OwnerCode' => $request['OwnerCode'] ?? null, //Owner Code
                'U_CashName' => $request['U_CashName'] ?? null, //Cash Customer  Name
                'U_CashNo' => $request['U_CashNo'] ?? null, // Cash Customer No
                'U_IDNo' => $request['U_IDNo'] ?? null, // Id no
                'NumAtCard' => $request['NumAtCard'] ?? null,
                'CurSource' => $request['CurSource'] ?? null,
                'DocTotal' => $request['DocTotal'] ?? null,
                'VatSum' => $request['VatSum'] ?? 0,
                'DocDate' => $request['DocDate'] ?? null, //PostingDate
                'TaxDate' => $request['TaxDate'] ?? null, //Document Date
                'DocDueDate' => $request['DocDueDate'] ?? null, // Delivery Date
                'ReqDate' => $request['ReqDate'],
                'CntctCode' => $request['CntctCode'] ?? null, //Contact Person
                'LicTradNum' => $request['LicTradNum'],
                'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //BaseKey
                'BaseType' => $request['BaseType'] ? $request['BaseType'] : null, //BaseKey

                //Inventory Transaction Values
                'Ref2' => $request['Ref2'] ? $request['Ref2'] : null, // Ref2
                'GroupNum' => $request['GroupNum'] ? $request['GroupNum'] : null, //[Price List]
                'ToWhsCode' => $request['ToWhsCode'] ? $request['ToWhsCode'] : null, //To Warehouse Code
                //SeriesDocument
                'DiscPrcnt' => $request['DiscPrcnt'] ?? 0, //Discount Percentages
                'DiscSum' => $request['DiscSum'] ?? null, // Discount Sum
                'BPLId' => $request['Branch'] ?? null,
                'U_SaleType' => $request['U_SaleType'] ?? null, // Sale Type
                'Comments' => $request['Comments'] ?? null, //comments
                'NumAtCard2' => $request['NumAtCard2'] ?? null,
                'JrnlMemo' => $request['JrnlMemo'] ?? null, // Journal Remarks
                'UseShpdGd' => $request['UseShpdGd'] ?? "N",
                'U_ServiceCall' => $request['U_ServiceCall'] ?? null,
                'U_DemoLocation' => $request['U_DemoLocation'] ?? null,
                'U_Technician' => $request['U_Technician'] ?? null,
                'U_Location' => $request['U_Location'] ?? null,
                'U_MpesaRefNo' => $request['U_MpesaRefNo'] ?? null,
                'U_PCash' => $request['U_PCash'] ?? null,
                'U_transferType' => $request['U_transferType'] ?? null,
                'U_SSerialNo' => $request['U_SSerialNo'] ?? null,
                'U_TypePur' => $request['U_TypePur'] ?? null,
                'U_NegativeMargin' => $request['U_NegativeMargin'] ?? null,
                'U_BaseDoc' => $request['U_BaseDoc'] ?? null,
                'ExtRef' => $request['ExtRef'] ?? null,
                'ExtRefDocNum' => $request['ExtRefDocNum'] ?? null,
                'ExtDocTotal' => $request['ExtDocTotal'] ?? null,
                'DataSource' => 'I',
                'UserSign' => $user->id,

            ];

            $newDoc = new $DocumentTables->ObjectHeaderTable(array_filter($NewDocDetails));
            $newDoc->save();

            $rowData = [];
            foreach ($request['document_lines'] as $key => $value) {
                $Dscription = $value['Dscription'];
                $UomCode = null;
                $unitMsr = null;

                if ($request['DocType'] == "S") {
                    if (!$Dscription) {
                        return response("Description Required", 421);
                    }
                }
                if ($request['DocType'] == "I") {
                    $product = OITM::Where('ItemCode', $value['ItemCode'])
                        ->first();

                    if (!$product) {
                        abort("Product with ItemCode:" . $value['ItemCode'] . " Does Not Exist", 500);
                    }
                    $Dscription = $product->ItemName;

                    $uom = OUOM::where('ExtRef', $value['UoMEntry'])->first();
                    $UomCode = $uom->id;
                    $unitMsr = $uom->UomName;
                }


                $quantity = $value['Quantity'];
                $PriceAfVAT = $value['PriceAfVAT'];
                $price = $value['Price'];

                $vatSum = $quantity * ($PriceAfVAT - $price);

                $rowdetails = [
                    'DocEntry' => $newDoc->id,
                    'OwnerCode' => $request['OwnerCode'],
                    'LineNum' => $request['LineNum'] ?? $key,
                    'ItemCode' => $value['ItemCode'],
                    'Dscription' => $Dscription,
                    'DocDate' => $request['DocDate'],
                    'Quantity' => $value['Quantity'],
                    'UomCode' => $UomCode, //
                    'Price' => $value['Price'], //
                    'DiscPrcnt' => $value['DiscPrcnt'] ?? 0,
                    'Rate' => $value['Rate'] ?? 0,
                    'TaxCode' => $value['VatGroup'] ?? null,
                    'PriceAfVAT' => $value['PriceAfVAT'],
                    'PriceBefDi' => $value['UnitPrice'],
                    'LineTotal' => $value['LineTotal'],
                    'VatSum' => $vatSum,
                    'WhsCode' => $value['WhsCode'] ?? null,
                    'SlpCode' => $request['SlpCode'] ?? null, //    Sales Employee
                    'Commission' => $value['Commission'] ?? null, //    Comm. %
                    'AcctCode' => $value['AcctCode'] ?? null, //    G/L Account
                    'OcrCode' => $value['OcrCode'], //    Dimension 1
                    'OcrCode2' => $value['OcrCode2'], //    Dimension 2
                    'OcrCode3' => $value['OcrCode3'], //    Dimension 3
                    'OcrCode4' => $value['OcrCode4'] ?? null, //    Dimension 4
                    'OcrCode5' => $value['OcrCode5'] ?? null, //    Dimension 5
                    'OpenQty' => $value['Quantity'] ?? 0, //    Open Inv. Qty

                    'BaseType' => $request['BaseType'] ?? $request['BaseType'], //    Base Type
                    'BaseRef' => $request['BaseRef'] ? $request['BaseRef'] : null, //    Base Ref.
                    'BaseEntry' => $request['BaseEntry'] ? $request['BaseEntry'] : null, //    Base Key
                    'BaseLine' => $request['BaseLine'], //    Base Row

                    'UomCode' => $value['UomCode'] ?? null, //    UoM Code
                    'unitMsr' => $unitMsr, //    UoM Name
                    'NumPerMsr' => array_key_exists('NumPerMsr', $value) ? $value['NumPerMsr'] : null, //    Items per Unit
                    'Text' => array_key_exists('Text', $value) ? $value['Text'] : null, //    Item Details
                    'OwnerCode' => $value['OwnerCode'] ?? null, //    Owner
                    'GTotal' => array_key_exists('GTotal', $value) ? $value['GTotal'] : null, //    Gross Total

                    'CogsOcrCod' => $value['OcrCode'],
                    'CogsOcrCo2' => $value['OcrCode2'],
                    'CogsOcrCo3' => $value['OcrCode3'],
                    'CogsOcrCo4' => $value['OcrCode4'] ?? null,
                    'CogsOcrCo5' => $value['OcrCode5'] ?? null,
                    'PQTReqDate' => $request['ReqDate'],

                    'BPLId' => $request['Branch'],
                    'WhsName' => isset($value['WhsName']) ? $value['WhsName'] : null,
                    'StockPrice' => $value['StockPrice'] ?? 0,
                    'LineStatus' => $value['LineStatus'],

                ];
                $rowItems = new $DocumentTables->pdi1[0]['ChildTable']($rowdetails);
                $rowItems->save();

                array_push($rowData, $rowItems);
            }

            // $eodc = EODC::where('ObjType', $ObjType)
            //     ->where('ExtRefDocNum', $request['ExtRefDocNum'])
            //     ->first();
            // if ($eodc) {
            //     $eodc->update([
            //         'ExtRef' => $request['ExtRef'],
            //         'DocEntry' => $newDoc->id,
            //         'DocNum' => $openingBalanceNumbering->NextNumber,
            //     ]);
            // }
            //  NumberingSeries::dispatch($openingBalanceNumbering->id);
            (new SystemDefaults())->updateNextNumberNumberingSeries($Numbering['id']);
            DB::connection("tenant")->commit();
            $newDoc->document_lines = $rowData;

            return $newDoc;
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            throw $th;
        }
    }

    public function getOpeningBalanceTransaction($ObjType)
    {
        // $uniqueFields = EODC::select('ExtFieldName')
        //     ->where('ObjType', $ObjType)
        //     ->groupBy('ExtFieldName')
        //     ->get();

        $data = [];
        // foreach ($uniqueFields as $key => $value) {
        //     $documents = EODC::where('ObjType', $ObjType)
        //         ->where('ExtFieldName', $value->ExtFieldName)
        //         ->pluck('ExtRefDocNum');

        //     $data1 = [
        //         $value->ExtFieldName => $documents,
        //     ];

        //     array_push($data, $data1);
        // }

        return $data;
    }

    /**
     * Post Document Gatement Magement Documents
     */

    public function createDocumentForGateManagementModule(Request $request)
    {
        $data = $request['data'];

        Log::info("TOTAL POSTED: " . count($data));

        foreach ($data as $key => $value) {

            if (!$value['LineDetails']) {
                // Log::info("WITHOUT LINE DETAILS");
                // Log::info($value);
                continue;
            }

            $data =  OGMS::firstOrCreate(
                [
                    'ObjType' => $value['ObjType'],
                    'ExtRef' => $value['ExtRef'],
                    'BaseEntry' => $value['BaseEntry'],
                    'BaseType' => $value['BaseType'],
                ],
                [
                    'ExtRefDocNum' => $value['ExtRefDocNum'],
                    'DocDate' => $value['DocDate'],
                    'GenerationDateTime' => Carbon::parse($value['GenerationDateTime'])->format('Y-m-d H:i:s'),
                    'DocTotal' => $value['DocTotal'],
                    'LineDetails' => $value['LineDetails'],
                ]
            );
        }
    }

    /**
     * Get Service Call
     */

    public function getServiceCall()
    {
        DB::connection("tenant")->beginTransaction();
        try {
            $data = OSCL::where('Transfered', 'N')
                ->get();

            foreach ($data as $key => $value) {
                $value->update([
                    'Transfered' => "Y",
                ]);
                $scl4 = SCL4::where('SrcvCallID', $value->id)->get();
                $expenses = [];
                foreach ($scl4 as $key => $val) {
                    $document = (new CommonService())->getSingleDocumentDetails($val->Object, $val->DocAbs);
                    if ($document) {
                        if ($document->ExtRef) {
                            $val->DocAbs = (int) $document->ExtRef;
                            $val->Object = (int) $val->Object;
                            array_push($expenses, $val);
                        }
                    }
                }
                $value->technician = OHEM::where('id', $value->technician)
                    ->value('empID');

                $value->expenses = $expenses;

                if ($value->insID) {
                    $value->insID = OINS::where('id', $value->insID)
                        ->value('ExtRef');
                }

                if ($value->origin) {
                    $value->origin = OSCO::where('id', $value->origin)
                        ->value('ExtRef');
                }

                $nnm1 = NNM1::where('id', $value->Series)->first();
                $value->Series = $nnm1->ExtRef;
            }

            DB::connection("tenant")->commit();
            return $data;
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            Log::error($th);
            throw $th;
        }
    }

    /**
     * Get Service Call
     */

    public function createOrUpdateSolutions(Request $request)
    {
        $user = Auth::user();

        $data = $request['data'];
        foreach ($data as $key => $val) {
            $solutionsData = OSLT::updateOrCreate([
                'ExtRef' => $val['ExtRef'],
            ], [
                'ItemCode' => $val['ItemCode'] ?? null,
                'StatusNum' => $val['StatusNum'] ?? null,
                'Owner' => $user->id,
                'Descriptio' => $val['Descriptio'] ?? null,
                'Subject' => $val['Subject'],
                'Symptom' => $val['Symptom'],
                'SltCode' => $val['SltCode'],
                'Cause' => $val['Cause'] ?? null,
            ]);

            $eodc = EODC::where('ObjType', 189)
                ->where('ExtRefDocNum', $val['SltCode'])
                ->first();
            if ($eodc) {
                $eodc->update([
                    'ExtRef' => $val['ExtRef'],
                    'DocEntry' => $solutionsData->id,

                ]);
            }
        }
    }

    /**
     * UpdateOrCreate Equipment Card
     */

    public function createOrUpdateEquipmentCard(Request $request)
    {
        $user = Auth::user();

        $data = $request['data'];

        try {
            foreach ($data as $key => $val) {
                $equipmentCard = OINS::updateOrCreate([
                    'ExtRef' => $val['ExtRef'],
                ], [
                    'insID' => $val['insID'],
                    'customer' => $val['customer'],
                    'OwnerCode' => $val['OwnerCode'] ?? null,
                    'custmrName' => $val['custmrName'],
                    'wrrntyStrt' => $val['wrrntyStrt'] ?? null,
                    'wrrntyEnd' => $val['wrrntyEnd'] ?? null,
                    'itemCode' => $val['itemCode'] ?? null,
                    'itemName' => $val['itemName'],
                    'manufSN' => $val['manufSN'] ?? null,
                    'internalSN' => $val['internalSN'],
                    'status' => $val['status'] ?? null,
                    'delivery' => $val['delivery'] ?? null,
                    'deliveryNo' => $val['deliveryNo'] ?? null,
                    'invoice' => $val['invoice'] ?? null,
                    'invoiceNum' => $val['invoiceNum'] ?? null,
                    'Transfered' => "Y",
                ]);

                $eodc = EODC::where('ObjType', 176)
                    ->where('ExtRefDocNum', $val['internalSN'])
                    ->first();
                if ($eodc) {
                    $eodc->update([
                        'ExtRef' => $val['ExtRef'],
                        'DocEntry' => $equipmentCard->id,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     *
     * Get Equipment Cards
     */

    public function getEquipmentCard()
    {
        $data = OINS::whereNull('ExtRef')
            ->where('Transfered', 'N')
            ->get();

        foreach ($data as $key => $headerVal) {
            $headerVal->update([
                'Transfered' => "Y",
            ]);
        }
        return $data;
    }
}
