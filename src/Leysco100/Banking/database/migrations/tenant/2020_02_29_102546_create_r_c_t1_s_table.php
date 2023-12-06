<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRCT1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_c_t1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('DocNum')
                ->references('id')->on('o_r_c_t_s')->nullable();
            $table->date('DueDate', 40)->nullable();
            $table->string('CheckNum', 254)->nullable();
            $table->integer('BankCode')
                ->references('id')->on('banks')->nullable();
            $table->string('Branch', 50)->nullable();
            $table->string('AcctNum', 50)->nullable();
            $table->string('Details', 254)->nullable();
            $table->string('Trnsfrable', 1)->default('N');
            $table->float('CheckSum', 19, 6)->nullable();
            $table->string('Currency', 8)
                ->references('id')->on('currencies')->nullable();
            $table->integer('Flags')->default(0);
            $table->integer('ObjType')->default('24');
            $table->string('LogInstanc', 1)->nullable();
            $table->integer('CountryCod')
                ->references('id')->on('countries')->nullable();
            $table->integer('CheckAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('CheckAbs', 1)->nullable();
            $table->string('BnkActKey', 1)->nullable();
            $table->string('ManualChk', 1)->nullable();
            $table->string('FiscalID', 1)->nullable();
            $table->string('OrigIssdBy', 1)->nullable();
            $table->string('Endorse', 1)->nullable();
            $table->string('EndorsChNo', 1)->nullable();
            $table->string('EnAcctNum', 1)->nullable();
            $table->string('EncryptIV', 1)->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
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
        Schema::dropIfExists('r_c_t1_s');
    }
}
