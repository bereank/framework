<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Services;


use App\Domains\InventoryAndProduction\Models\SRI1;
use Leysco100\Shared\Models\Shared\Models\APDI;

/**
 * Purchase and Marketing Document Shared Service
 */
class GeneralDocumentService
{
    /**
     * Get Serial Numbers For specific Object and Row
     *
     */
    public function getDocumentLinesSerialNumbers(int $ObjType, int $DocEntry, int $LineNum)
    {
        $SerialNumbers = SRI1::where('LineNum', $LineNum)
            ->where('BaseType', $ObjType)
            ->where('BaseEntry', $DocEntry)
            ->get();

        return $SerialNumbers;
    }

    /**
     * Get Serial Numbers For specific Object and Row
     *
     */
    public function getBaseDocumentLinesSerialNumbers(int $ObjType, int $DocEntry, int $LineNum)
    {
        $SerialNumbers = SRI1::where('LineNum', $LineNum)
            ->where('BaseType', $ObjType)
            ->where('BaseEntry', $DocEntry)
            ->get();

        return $SerialNumbers;
    }

    public function comporeRowToBaseRow(int $ObjType, int $DocEntry): void
    {
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $data = $DocumentTables->ObjectHeaderTable::with('rows')
            ->where('id', $DocEntry)
            ->first();

        /**
         * Compare Rows
         */

        $allRowData = $data->rows;

        $totalNotSimilarRows = count($allRowData);
        foreach ($allRowData as $key => $rowData) {
            if (!is_null($rowData->BaseLine)) {
                $baseRowData = (new GeneralDocumentSerivce())
                    ->getBaseLineDetails($rowData->BaseType, $rowData->BaseEntry, $rowData->BaseLine);

                if ($baseRowData) {
                    if ($baseRowData->ItemCode != $rowData->ItemCode) {
                        $totalNotSimilarRows--;
                        $rowData->update([
                            'BaseType' => null,
                            'BaseEntry' => null,
                            'BaseLine' => null,
                        ]);
                    }
                }

                if (!$baseRowData) {
                    $totalNotSimilarRows--;
                    $rowData->update([
                        'BaseType' => null,
                        'BaseEntry' => null,
                        'BaseLine' => null,
                    ]);
                }
            }

            if (is_null($rowData->BaseLine)) {
                $totalNotSimilarRows--;
                $rowData->update([
                    'BaseType' => null,
                    'BaseEntry' => null,
                    'BaseLine' => null,
                ]);
            }
        }

        if ($totalNotSimilarRows <= 0) {
            $data->update([
                'BaseType' => null,
                'BaseEntry' => null,
            ]);
        }
    }

    public function getBaseLineDetails(int $BaseType, int $BaseEntry, int $BaseLine)
    {
        $BaseTables = APDI::with('pdi1')
            ->where('ObjectID', $BaseType)
            ->first();

        $rowData = $BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $BaseEntry)
            ->where('LineNum', $BaseLine)
            ->first();

        return $rowData;
    }
}
