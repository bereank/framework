<?php

namespace Leysco100\Shared\Actions\Helpers;

use Leysco100\Shared\Models\CUFD;
use Leysco100\Shared\Models\UserDict;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
     *
     */
    public function __construct(
        protected string $tableName,
        protected string $fieldName,
        protected string $fieldDescription,
        protected string $fieldType,
        protected int $fieldSize,
        protected string $ObjType
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->fieldDescription = $fieldDescription;
        $this->fieldType = $fieldType;
        $this->fieldSize = $fieldSize;
        $this->ObjType = $ObjType;
    }

    public function handle()
    {


        Schema::connection('tenant')->table($this->tableName, function (Blueprint $table) {
            $fieldName = "U_" . $this->fieldName;
            CUFD::firstOrCreate([
                'FieldName' =>  $fieldName,
                'TableName' => $this->tableName
            ], [
                'FieldDescription' => $this->fieldDescription,
                'FieldType' => $this->fieldType,
                'ObjType' => $this->ObjType,
                'FieldSize' => $this->fieldSize
            ]);


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
