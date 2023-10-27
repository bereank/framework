<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsValidationTable extends Migration
{
    public function up()
    {
        Schema::create('docs_validation', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType');
            $table->string('TableName')->nullable();
            $table->integer('CategoryType')->comment('1=>header, 2=> lines')->nullable();
            $table->boolean('Status')->nullable()->default(1);
            $table->integer('UserSign');
            $table->integer('UserSign2')->nullable();
            $table->string('FieldName');
            $table->string('Label')->nullable();
            $table->string('FieldType');
            $table->integer('FieldMinLength')->nullable();
            $table->integer('FieldMaxLength')->nullable();
            $table->boolean('IsNullable')->nullable()->default(0);
            $table->string('FieldDefaultValue')->nullable();
            $table->boolean('IsUnique')->nullable()->default(0);
            $table->json('StartsWith')->nullable();
            $table->json('EndsWith')->nullable();
            $table->integer('Size')->nullable();
            $table->string('Regex')->nullable();
            $table->boolean('RtrnOnCreate')->nullable()->default(1);
            $table->boolean('RtrnOnGet')->nullable()->default(1);
            $table->string('RTable', 50)->nullable();
            $table->string('RField', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('docs_validation');
    }
}
