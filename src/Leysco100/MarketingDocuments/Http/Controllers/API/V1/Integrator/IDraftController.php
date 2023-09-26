<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Actions\TransactionInventoryEffectAction;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Models\Banking\Services\BankingDocumentService;
use Leysco100\Shared\Models\MarketingDocuments\Models\DRF1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OATS;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Leysco100\Shared\Models\MarketingDocuments\Services\GeneralDocumentService;
use Leysco100\Shared\Models\Shared\Models\APDI;

class IDraftController extends Controller
{
    public function createDocumentFromDraft(Request $request, $draftKey, $ObjType)
    {
        DB::connection("tenant")->beginTransaction();
        try {

            if ($ObjType == 112) {
                return null;
            }

            $draftDetailsHeader = ODRF::where('ExtRef', $draftKey)
                ->first();

            $originalDocNum = $draftDetailsHeader->DocNum;

            if (!$draftDetailsHeader) {
                return null;
            }

            if ($draftDetailsHeader->DocStatus == "C") {
                return null;
            }

            $draftDetailsRows = DRF1::where('DocEntry', $draftDetailsHeader->id)->get();

            /**
             * Attachments
             */

            $attachments = OATS::where('DocEntry', $draftDetailsHeader->id)
                ->where('ObjType', 112)->get();

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

            $documentHeader = $draftDetailsHeader;
            $documentHeader->DocNum = $DocNum;
            $documentHeader->draftKey = $draftDetailsHeader->id;
            $documentHeader->ExtRef = $request['idOrder'];
            $documentHeader->ExtRefDocNum = $request['DocNoOrder'];

            $newDoc = new $targetTables->ObjectHeaderTable($documentHeader->toArray());

            $newDoc->save();

            foreach ($draftDetailsRows as $key => $row) {
                $row->DocEntry = $newDoc->id;
                $rowItems = new $targetTables->pdi1[0]['ChildTable']($row->toArray());
                $rowItems->save();
                $serialNumbers = (new GeneralDocumentService())->getDocumentLinesSerialNumbers(112, $draftDetailsHeader->id, $row->id);
                foreach ($serialNumbers as $key => $serial) {
                    $serial->update([
                        "LineNum" => $rowItems->id,
                        "BaseType" => $draftDetailsHeader->ObjType,
                        "BaseEntry" => $newDoc->id,
                    ]);
                }
            }
            foreach ($attachments as $key => $row) {
                $row->DocEntry = $newDoc->id;
                $row->ObjType = $newDoc->ObjType;
                $rowItems = new OATS($row->toArray());
                $rowItems->save();
            }

            $newDoc->LS100Id = $newDoc->id;
            $newDoc->LS100DocNum = $newDoc->DocNum;
            $newDoc->objType = $newDoc->ObjType;

            if ($newDoc->ObjType == 205) {
                $newDoc->objType = 1470000113;
            }
            if ($newDoc->ObjType == 66) {
                $newDoc->objType = 1250000001;
            }
            $newDoc->idOrder = $newDoc->ExtRef;

            /**
             * Close Draft
             */

            $draftDetailsHeader = ODRF::where('ExtRef', $draftKey)
                ->first();
            $draftDetailsHeader->update([
                'DocStatus' => "C",
            ]);

            /**
             * Document Payments
             */
            if ($ObjType == 13) {
                $newDoc->payments = (new BankingDocumentService())->getInvoicePayment($draftDetailsHeader->id,$newDoc->id);
            }

            $ivoicePaymentDetails = RCT2::where('DocEntry', $draftDetailsHeader->id)
                ->where('InvoiceId', $originalDocNum)->get();

            foreach ($ivoicePaymentDetails as $key => $value) {
                $value->update([
                    'InvoiceId' => $newDoc->DocNum,
                    'DocEntry' => $newDoc->id,
                ]);
            }

            if ($ObjType != 112) {

                (new TransactionInventoryEffectAction())->transactionInventoryEffect($ObjType, $newDoc->id);

            }

            NumberingSeries::dispatch($newDoc->Series);
            DB::connection("tenant")->commit();
            return $newDoc;
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return response()
                ->json([
                    'message' => $th->getMessage(),
                ], 500);
        }
    }
}
