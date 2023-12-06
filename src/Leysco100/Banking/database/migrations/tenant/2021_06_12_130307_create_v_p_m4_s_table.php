<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVPM4STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v_p_m4_s', function (Blueprint $table) {
            $table->id();
            $table->integer('DocNum')
                ->references('id')->on('o_v_p_m_s')->nullable();
            $table->integer('LineId')->nullable();
            $table->integer('AcctCode')->nullable();
            $table->float('SumApplied', 19, 2)->nullable();
            $table->string('AppliedFC', 1)->nullable();
            $table->string('AppliedSys', 1)->nullable();
            $table->string('Descrip', 1)->nullable();
            $table->integer('VatGroup')->nullable();
            $table->string('VatPrcnt', 1)->nullable();
            $table->string('AcctName')->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('Project', 1)->nullable();
            $table->double('GrossAmnt', 1)->nullable();
            $table->string('GrssAmntFC', 1)->nullable();
            $table->string('GrssAmntSC', 1)->nullable();
            $table->float('AmntBase', 19, 2)->nullable();
            $table->string('VatAmnt', 1)->nullable();
            $table->string('VatAmntFC', 1)->nullable();
            $table->string('VatAmntSC', 1)->nullable();
            $table->string('UserChaVat', 1)->nullable();
            $table->string('TaxTypeID', 1)->nullable();
            $table->string('OcrCode', 8)->nullable();
            $table->integer('OcrCode2')->nullable();
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
            $table->string('Section', 1)->nullable();
            $table->string('AsseType', 1)->nullable();
            $table->string('LocCode', 1)->nullable();
            $table->string('MatType', 1)->nullable();
            $table->string('EquVatPer', 1)->nullable();
            $table->string('EquVatSum', 1)->nullable();
            $table->string('EquVatSumF', 1)->nullable();
            $table->string('EquVatSumS', 1)->nullable();
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
        Schema::dropIfExists('v_p_m4_s');
    }
}
