<?php

namespace Leysco100\Shared\Actions\Helpers;

use Leysco100\Shared\Models\CUFD;
use Leysco100\Shared\Models\UserDict;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Leysco100\Shared\Models\Shared\Models\UFD1;

class CreateUDFHelperAction
{

    /**
     * Create User Define Fields
     *
     * @var string $tableName
     * @var string $fieldName
     * @var string $fieldDescription
     * @var string $fieldType
     * @var int $fieldSize
     * @var string $ObjType
     * @var  $RField
     * @var $RTable
     * @var $ValidRule
     * @var $FldValue
     * @var $DispField
     * @var $Descr
     */
    public function __construct(
        protected string $tableName,
        protected string $fieldName,
        protected string $fieldDescription,
        protected string $fieldType,
        protected int $fieldSize,
        protected  $RField,
        protected $RTable,
        protected $ValidRule,
        protected $FldValue,
        protected $DispField,
        protected $Descr,

        protected string $ObjType
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->fieldDescription = $fieldDescription;
        $this->fieldType = $fieldType;
        $this->fieldSize = $fieldSize;
        $this->RField = $RField;
        $this->RTable = $RTable;
        $this->ValidRule = $ValidRule;
        $this->FldValue = $FldValue;
        $this->DispField = $DispField;
        $this->Descr = $Descr;
        $this->ObjType = $ObjType;
    }

    public function handle()
    {


        Schema::connection('tenant')->table($this->tableName, function (Blueprint $table) {
            $fieldName = $this->fieldName;
            if (strpos($fieldName, 'U_') !== 0) {
                $fieldName = "U_" . $fieldName;
            }
            $newRecord =     CUFD::firstOrCreate([
                'FieldName' =>  $fieldName,
                'TableName' => $this->tableName
            ], [
                'FieldDescription' => $this->fieldDescription,
                'FieldType' => $this->fieldType,
                'ObjType' => $this->ObjType,
                'FieldSize' => $this->fieldSize,
                'DispField' => $this->DispField ?? null,
                'ValidRule' => $this->ValidRule ?? null,
                'RTable' => $this->RTable ?? null,
                'RField' => $this->RField ?? null,

            ]);
            if ($this->ValidRule == 2) {

                foreach ($this->FldValue as $key => $value) {
                    if (array_key_exists($key,  $this->Descr)) {
                        $UFD1 =  UFD1::create([
                            'TableID' => 1,
                            'FieldID' =>   $newRecord->id,
                            'IndexID' => $key,
                            'FldValue' => $value,
                            'Descr' => $this->Descr[$key]
                        ]);
                    }
                }
            }

            if ($this->checkIfColumnExist($this->tableName,   $fieldName)) {
                return true;
            }



            if ($this->fieldType == 'string') {
                $table->string($fieldName, $this->fieldSize)->comment($this->fieldDescription);
            }

            if ($this->fieldType == 'integer') {
                $table->integer($fieldName)->comment($this->fieldDescription);
            }

            if ($this->fieldType == 'decimal') {
                $table->decimal($fieldName, $this->fieldSize, 3)->comment($this->fieldDescription);
            }

            if ($this->fieldType == 'date') {
                $table->date($fieldName)->comment($this->fieldDescription);
            }


            if ($this->fieldType == 'timestamp') {
                $table->timestamp($fieldName)->comment($this->fieldDescription);
            }

            // $table->string('FieldName');
            // $table->string('FieldDescription')->nullable();
            // $table->string('FieldType')->nullable();
            // $table->string('FieldIndex')->nullable();
            // $table->integer('ObjType');
            // $table->string('TableName')->nullable();
            // $table->integer('FieldSize');
            // $table->string('DefaultValue')->nullable();


        });
    }

    public function checkIfColumnExist($table, $fieldName)
    {

        if (Schema::connection('tenant')->hasColumn($table, $fieldName)) {
            return true;
        }

        return false;
    }
}
