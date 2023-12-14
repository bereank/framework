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
        Schema::create('u_f_d1_s', function (Blueprint $table) {
            $table->id();
            $table->Integer('TableID')->nullable();
            $table->Integer('FieldID')->nullable();
            $table->Integer('IndexID')->nullable();
            $table->string('FldValue', 254)->nullable();
            $table->string('Descr', 254)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_f_d1_s');
    }
};
