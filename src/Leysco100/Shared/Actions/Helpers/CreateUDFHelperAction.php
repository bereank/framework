<?php

namespace Leysco100\Shared\Actions\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Leysco100\Shared\Models\UserDict;

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
     * 
     */
    public function __construct(protected string $tableName, protected string $fieldName, protected string $fieldDescription, protected string $fieldType, protected int $fieldSize)
    {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->fieldDescription = $fieldDescription;
        $this->fieldType = $fieldType;
        $this->fieldSize = $fieldSize;
    }

    public function handle()
    {
        Schema::connection('tenant')->table($this->tableName, function (Blueprint $table) {

            UserDict::firstOrCreate([
                'FieldName' => $this->fieldName,
                'TableName' => $this->tableName
            ],[
                'FieldDescription' => $this->fieldDescription,
                'FieldType' => $this->fieldType,
                'ObjType' => 4,
                'FieldSize' =>$this->fieldSize
            ]);


            if ($this->checkIfColumnExist($this->tableName, $this->fieldName)) {
                return true;
            }

        

            if ($this->fieldType =='string') {
            $table->string($this->fieldName,$this->fieldSize)->comment($this->fieldDescription);

            }

            if ($this->fieldType =='integer') {
            $table->integer($this->fieldName)->comment($this->fieldDescription);

            }

            if ($this->fieldType =='decimal') {
            $table->decimal($this->fieldName,$this->fieldSize,3)->comment($this->fieldDescription);

            }

            if ($this->fieldType =='date') {
                $table->date($this->fieldName)->comment($this->fieldDescription);
    
            }

            
            if ($this->fieldType =='timestamp') {
                $table->timestamp($this->fieldName)->comment($this->fieldDescription);
    
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
