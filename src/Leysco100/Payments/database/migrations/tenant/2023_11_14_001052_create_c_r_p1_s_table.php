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
        Schema::create('c_r_p1_s', function (Blueprint $table) {
            $table->id();
            $table->string('DocNum')->nullable();
            $table->string('TransID')->nullable();
            $table->string('DocEntry')->nullable();
            $table->string('AllocatedAmount')->nullable();
            $table->dateTime('DocDate')->nullable();
            $table->string('OwnerPhone')->nullable();
            $table->string('CreditCur')->nullable();
            $table->string('CrCardNum')->nullable();
            $table->string('CreditCard')->nullable();
            $table->string('CreditAcct')->nullable();
            $table->string('ObjType')->nullable();
            $table->string('CrTypeCode')->nullable();
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_r_p1_s');
    }
};
