<?php

namespace Leysco100\Shared\Actions\Helpers;

class HideTableRowsFieldsPerDocumentAction
{


    public function __construct(protected int $ObjType, protected $tableRow)
    {
       $this->ObjType = $ObjType;
       $this->tableRow = $tableRow;
    }

    public function handle()
    {
        $ObjType = $this->ObjType;
        $tableRow = $this->tableRow;

        if ($ObjType == 205) {
            if ($tableRow->value == "ItemCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "ItemCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "DiscPrcnt") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "WhsCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "WhsCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "UomCode") {
                $tableRow->Visible = "N";
            }

            if ($tableRow->value == "Rate") {
                $tableRow->Visible = "N";
            }
        }

        // if ($ObjType == 66) {

        //     if ($tableRow->value == "WhsName") {
        //         $tableRow->Visible = "N";
        //     }

        //     if ($tableRow->value == "FromWhsCod") {
        //         $tableRow->Visible = "Y";
        //     }

        //     if ($tableRow->value == "ToWhsCode") {
        //         $tableRow->Visible = "Y";
        //     }

        //     if ($tableRow->value == "DiscPrcnt") {
        //         $tableRow->Visible = "N";
        //     }
        // }

        return $tableRow;
    }
}
