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
        Schema::create('c_u_f_d', function (Blueprint $table) {
            $table->id();
            $table->string('FieldName');
            $table->string('FieldDescription')->nullable();
            $table->string('FieldType')->nullable();
            $table->string('FieldIndex')->nullable();
            $table->integer('ObjType');
            $table->string('TableName')->nullable();
            $table->integer('FieldSize');
            $table->boolean('NotNull')->default(0);
            $table->string('RTable', 20)->nullable();
            $table->string('RField', 20)->nullable();
            $table->string('DispField', 50)->nullable();
            $table->string('RelUDO', 20)->nullable();
            $table->string('Action', 20)->nullable();
            $table->char('Sys')->nullable();
            $table->string('ValidRule', 20)->nullable();
            $table->string('RelSO', 20)->nullable();
            $table->string('RThrdPTab', 20)->nullable();
            $table->string('RThrdPFld', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_u_f_d');
    }
};
