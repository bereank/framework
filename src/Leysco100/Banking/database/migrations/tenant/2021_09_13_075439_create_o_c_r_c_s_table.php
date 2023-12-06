<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCRCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_r_c_s', function (Blueprint $table) {
            $table->id();
            $table->integer('CreditCard')->nullable();
            $table->string('CardName', 30)->nullable();
            $table->string('AcctCode', 15)->nullable();
            $table->string('Phone', 1)->nullable();
            $table->string('CompanyId', 1)->nullable();
            $table->string('Locked', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('UpdateDate', 1)->nullable();
            $table->string('IntTaxCode', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->string('Country', 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('o_c_r_c_s');
    }
}
