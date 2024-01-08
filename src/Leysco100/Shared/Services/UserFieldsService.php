<?php

namespace Leysco100\Shared\Services;

use Carbon\Carbon;
use Leysco100\Shared\Models\CUFD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Shared\Models\APDI;

class UserFieldsService
{

    public function processUDF($item)
    {

        $form = APDI::with('pdi1')
            ->where('ObjectID', $item['doctype'])
            ->first();
        if (!$form) {
            return;
        }

        $headerTable = (new $form->ObjectHeaderTable)->getTable();

        $userFields = CUFD::where('ObjType', $item['doctype'])
            ->where("TableName", $headerTable)
            ->with('items:id,FieldID,FldValue as Value,Descr')
            ->select(
                "id",
                "FieldName",
                "FieldDescription",
                "NotNull",
                "ValidRule",
                "RTable",
                "DispField",
                "RField",
                "FieldType"
            )
            ->get();
        if (!empty($userFields)) {
            foreach ($userFields as &$userFld) {
                if ($userFld->ValidRule == 3) {
                    if ($userFld->relationLoaded('items')) {
                        $userFld->unsetRelation('items');
                    }
                    $object = APDI::find($userFld->RTable);
                    $res = $object->ObjectHeaderTable::select(
                        'id',
                        DB::raw("{$userFld->RField} as  Value"),
                        DB::raw("{$userFld->DispField} as Descr")
                    )
                        ->get();

                    $userFld->items = $res;
                }
            }
        }
        $line_table = [];
        if (!empty($form->pdi1[0])) {
            $line_table = (new $form->pdi1[0]['ChildTable'])->getTable();
        }
        $LineuserFields = CUFD::where('ObjType', $item['doctype'])
            ->where("TableName", $line_table)
            ->with('items:id,FieldID,FldValue as Value,Descr')
            ->select(
                "id",
                "FieldName",
                "FieldDescription",
                "NotNull",
                "ValidRule",
                "RTable",
                "DispField",
                "RField"
            )
            ->get();
        if (!empty($LineuserFields)) {
            foreach ($LineuserFields as &$lineFld) {
                if ($lineFld->ValidRule == 3) {
                    if ($lineFld->relationLoaded('items')) {
                        $lineFld->unsetRelation('items');
                    }
                    $object = APDI::find($lineFld->RTable);
                    $res = $object->ObjectHeaderTable::select(
                        'id',
                        DB::raw("{$lineFld->RField} as  Value"),
                        DB::raw("{$lineFld->DispField} as Descr")
                    )
                        ->get();

                    $lineFld->items = $res;
                }
            }
        }
        $item['HeaderUserFields'] = $userFields;
        $item['LineUserFields'] = $LineuserFields;
        return $item;
    }
}
