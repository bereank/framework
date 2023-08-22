<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_dicts', function (Blueprint $table) {
            $table->id();
            $table->string('FieldName');
            $table->string('FieldDescription')->nullable();
            $table->string('FieldType')->nullable();
            $table->string('FieldIndex')->nullable();
            $table->integer('ObjType');
            $table->string('TableName')->nullable();
            $table->integer('FieldSize');
            $table->string('DefaultValue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dicts');
    }
};
