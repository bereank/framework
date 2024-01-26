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
            $table->string('CreditCard')->nullable();
            $table->string('CardName')->nullable();
            $table->string('AcctCode')->nullable();
            $table->string('Phone')->nullable();
            $table->string('CompanyId')->nullable();
            $table->string('Locked')->nullable();
            $table->string('DataSource')->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('LogInstanc')->nullable();
            $table->string('UpdateDate')->nullable();
            $table->string('IntTaxCode')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->string('Country')->nullable();
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
