<?php

namespace Leysco100\Gpm\Services;

use Leysco100\Shared\Models\Marketing\Models\OGMS;




class DocumentsService
{
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
}
