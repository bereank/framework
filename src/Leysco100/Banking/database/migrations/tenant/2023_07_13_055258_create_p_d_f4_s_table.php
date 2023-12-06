<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_d_f4_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('DocNum')
                ->references('id')->on('o_p_d_f_s')->nullable();
            $table->string('LineId', 1)->nullalbe();
            $table->string('AcctCode', 1)->nullalbe();
            $table->string('SumApplied', 1)->nullalbe();
            $table->string('AppliedFC', 1)->nullalbe();
            $table->string('AppliedSys', 1)->nullalbe();
            $table->string('Descrip', 1)->nullalbe();
            $table->string('VatGroup', 1)->nullalbe();
            $table->string('VatPrcnt', 1)->nullalbe();
            $table->string('AcctName', 1)->nullalbe();
            $table->string('ObjType', 1)->nullalbe();
            $table->string('LogInstanc', 1)->nullalbe();
            $table->string('OcrCode', 1)->nullalbe();
            $table->string('Project', 1)->nullalbe();
            $table->string('GrossAmnt', 1)->nullalbe();
            $table->string('GrssAmntFC', 1)->nullalbe();
            $table->string('GrssAmntSC', 1)->nullalbe();
            $table->string('AmntBase', 1)->nullalbe();
            $table->string('VatAmnt', 1)->nullalbe();
            $table->string('VatAmntFC', 1)->nullalbe();
            $table->string('VatAmntSC', 1)->nullalbe();
            $table->string('UserChaVat', 1)->nullalbe();
            $table->string('TaxTypeID', 1)->nullalbe();
            $table->string('OcrCode2', 1)->nullalbe();
            $table->string('OcrCode3', 1)->nullalbe();
            $table->string('OcrCode4', 1)->nullalbe();
            $table->string('OcrCode5', 1)->nullalbe();
            $table->string('Section', 1)->nullalbe();
            $table->string('AsseType', 1)->nullalbe();
            $table->string('LocCode', 1)->nullalbe();
            $table->string('MatType', 1)->nullalbe();
            $table->string('EquVatPer', 1)->nullalbe();
            $table->string('EquVatSum', 1)->nullalbe();
            $table->string('EquVatSumF', 1)->nullalbe();
            $table->string('EquVatSumS', 1)->nullalbe();
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
        Schema::dropIfExists('p_d_f4_s');
    }
};
