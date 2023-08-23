<?php

namespace Leysco100\Shared\Models\Shared\Services;


use Leysco100\Shared\Models\OSCL;
use Leysco100\Shared\Models\SCL2;
use Leysco100\Shared\Models\SCL4;
use Leysco100\Shared\Models\Shared\Models\APDI;

/**
 * Common Item Services
 */
class ServiceCallService
{
    /**
     *  Map Expense with Service Call
     * @param string $ObjType
     *  @param int $DocEntry
     * @param int $serviceCallId
     */
    public function mapServiceCallWithExpenseDocument(int $ObjType, int $DocEntry, int $serviceCallId)
    {
        $SERVICE_CALL_OBJECT_TYPE = 191;
        $DRAFT_DOC_OBJECT_TYPE = 112;
        $documentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $documentHeader = $documentTables->ObjectHeaderTable::where('id', $DocEntry)
            ->first();

        $scl4Data = SCL4::updateOrcreate([
            'SrcvCallID' => $serviceCallId,
            'DocAbs' => $DocEntry,
        ], [
            'Line' => 1,
            'DocPstDate' => $documentHeader->DocDate,
            'DocNumber' => $documentHeader->DocNum,
            'ObjectType' => $SERVICE_CALL_OBJECT_TYPE,
            'Object' => $ObjType,
            'UserSign' => $documentHeader->UserSign,
        ]);

        if ($ObjType != $DRAFT_DOC_OBJECT_TYPE) {
            $documentRowData = $documentTables->pdi1[0]['ChildTable']::where('DocEntry', $DocEntry)
                ->orderBy('LineNum', 'asc')
                ->get();

            foreach ($documentRowData as $key => $rowData) {
                $scl2Data = SCL2::updateOrcreate([
                    'SrcvCallID' => $serviceCallId,
                    'Line' => $key,
                    'ItemCode' => $rowData->ItemCode,
                    'ItemName' => $rowData->Dscription,
                    'ObjectType' => $SERVICE_CALL_OBJECT_TYPE,
                    'CreateDate' => $documentHeader->DocDate,
                    'UserSign' => $documentHeader->UserSign,
                    'VisOrder' => $key + 1,
                ]);
            }
        }
    }

    /**
     * Sync
     */

    public function updateServiceCallExpenseDetails(int $ObjType, int $oldDocEntry, int $newDocEntry)
    {
        $SERVICE_CALL_OBJECT_TYPE = 191;
        $documentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        $documentHeader = $documentTables->ObjectHeaderTable::where('id', $newDocEntry)
            ->first();

        $documentRowData = $documentTables->pdi1[0]['ChildTable']::where('DocEntry', $newDocEntry)
            ->orderBy('LineNum', 'asc')
            ->get();

        $scl4 = SCL4::where('Object', 112)
            ->where('DocAbs', $oldDocEntry)->first();

        if (!$scl4) {
            return null;
        }
        $scl4->update([
            'Object' => $ObjType,
            'DocNumber' => $documentHeader->DocNum,
            'DocAbs' => $newDocEntry,
        ]);

        foreach ($documentRowData as $key => $rowData) {
            $scl2Data = SCL2::updateOrcreate([
                'SrcvCallID' => $scl4->SrcvCallID,
                'Line' => $key,
                'ItemCode' => $rowData->ItemCode,
                'ItemName' => $rowData->Dscription,
                'ObjectType' => $SERVICE_CALL_OBJECT_TYPE,
                'CreateDate' => $documentHeader->DocDate,
                'UserSign' => $documentHeader->UserSign,
                'VisOrder' => $key + 1,
            ]);
        }

        /**
         * Mark Service Call To be sycned
         */
        $serviceCall = OSCL::where('id', $scl4->SrcvCallID)->first();
        $serviceCall->update([
            'Transfered' => "N",
        ]);
    }
}
