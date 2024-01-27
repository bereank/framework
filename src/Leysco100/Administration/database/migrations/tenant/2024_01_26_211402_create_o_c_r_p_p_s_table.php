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
        Schema::create('o_c_r_p_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('CrTypeCode')->nullable();
            $table->string('CrTypeName')->nullable();
            $table->string('CreditCard')->nullable();
            $table->integer('DueTerms')->nullable();
            $table->decimal('MinCredit', 10, 2)->nullable();
            $table->decimal('MinToPay', 10, 2)->nullable();
            $table->string('MaxValid')->nullable();
            $table->integer('InstalMent')->nullable();
            $table->string('Locked', 1)->default('N')->nullable();
            $table->string('DataSource')->default('I')->nullable();
            $table->integer('UserSign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_c_r_p_p_s');
    }
};
