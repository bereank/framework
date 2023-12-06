<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJDT1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('j_d_t1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('TransId')->nullable();
            $table->integer('Line_ID')->nullable();
            $table->integer('Account')->nullable();
            $table->float('Debit', 19, 6)->nullable();
            $table->float('Credit', 19, 6)->nullable();
            $table->float('SYSCred', 19, 6)->nullable();
            $table->float('SYSDeb', 19, 6)->nullable();
            $table->string('FCDebit', 1)->nullable();
            $table->string('FCCredit', 1)->nullable();
            $table->string('FCCurrency', 1)->nullable();
            $table->date('DueDate')->nullable();
            $table->string('SourceID', 1)->nullable();
            $table->string('SourceLine', 1)->nullable();
            $table->string('ShortName', 1)->nullable();
            $table->string('IntrnMatch', 1)->nullable();
            $table->string('ExtrMatch', 1)->nullable();
            $table->string('ContraAct', 1)->nullable();
            $table->string('LineMemo')->nullable();
            $table->string('Ref3Line', 1)->nullable();
            $table->string('TransType', 1)->nullable();
            $table->date('RefDate')->nullable();
            $table->date('Ref2Date')->nullable();
            $table->string('Ref1', 100)->nullable();
            $table->string('Ref2', 100)->nullable();
            $table->string('CreatedBy', 1)->nullable();
            $table->string('BaseRef', 1)->nullable();
            $table->string('Project', 1)->nullable();
            $table->string('TransCode', 1)->nullable();
            $table->string('ProfitCode', 1)->nullable();
            $table->date('TaxDate')->nullable();
            $table->string('SystemRate', 1)->nullable();
            $table->string('MthDate', 1)->nullable();
            $table->string('ToMthSum', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('BatchNum', 1)->nullable();
            $table->string('FinncPriod', 1)->nullable();
            $table->string('RelTransId', 1)->nullable();
            $table->string('RelLineID', 1)->nullable();
            $table->string('RelType', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('VatGroup', 1)->nullable();
            $table->string('BaseSum', 1)->nullable();
            $table->string('VatRate', 1)->nullable();
            $table->string('Indicator', 1)->nullable();
            $table->string('AdjTran', 1)->nullable();
            $table->string('RevSource', 1)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('VatDate', 1)->nullable();
            $table->string('PaymentRef', 1)->nullable();
            $table->string('SYSBaseSum', 1)->nullable();
            $table->string('MultMatch', 1)->nullable();
            $table->string('VatLine', 1)->nullable();
            $table->string('VatAmount', 1)->nullable();
            $table->string('SYSVatSum', 1)->nullable();
            $table->string('Closed', 1)->nullable();
            $table->string('GrossValue', 1)->nullable();
            $table->string('CheckAbs', 1)->nullable();
            $table->string('LineType', 1)->nullable();
            $table->string('DebCred', 1)->nullable();
            $table->string('SequenceNr', 1)->nullable();
            $table->string('StornoAcc', 1)->nullable();
            $table->string('BalDueDeb', 1)->nullable();
            $table->string('BalDueCred', 1)->nullable();
            $table->string('BalFcDeb', 1)->nullable();
            $table->string('BalFcCred', 1)->nullable();
            $table->string('BalScDeb', 1)->nullable();
            $table->string('BalScCred', 1)->nullable();
            $table->string('IsNet', 1)->nullable();
            $table->string('DunWizBlck', 1)->nullable();
            $table->string('DunnLevel', 1)->nullable();
            $table->string('DunDate', 1)->nullable();
            $table->string('TaxType', 1)->nullable();
            $table->string('TaxPostAcc', 1)->nullable();
            $table->string('StaCode', 1)->nullable();
            $table->string('StaType', 1)->nullable();
            $table->string('TaxCode', 1)->nullable();
            $table->string('ValidFrom', 1)->nullable();
            $table->string('GrossValFc', 1)->nullable();
            $table->string('LvlUpdDate', 1)->nullable();
            $table->string('OcrCode2', 8)->nullable()->comment("Costing Center 2");
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
            $table->string('MIEntry', 1)->nullable();
            $table->string('MIVEntry', 1)->nullable();
            $table->string('ClsInTP', 1)->nullable();
            $table->string('CenVatCom', 1)->nullable();
            $table->string('MatType', 1)->nullable();
            $table->string('PstngType', 1)->nullable();
            $table->string('ValidFrom2', 1)->nullable();
            $table->string('ValidFrom3', 1)->nullable();
            $table->string('ValidFrom4', 1)->nullable();
            $table->string('ValidFrom5', 1)->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('WTaxCode', 1)->nullable();
            $table->string('EquVatRate', 1)->nullable();
            $table->string('EquVatSum', 1)->nullable();
            $table->string('SYSEquSum', 1)->nullable();
            $table->string('TotalVat', 1)->nullable();
            $table->string('SYSTVat', 1)->nullable();
            $table->string('WTLiable', 1)->nullable();
            $table->string('WTLine', 1)->nullable();
            $table->string('WTApplied', 1)->nullable();
            $table->string('WTAppliedS', 1)->nullable();
            $table->string('WTAppliedF', 1)->nullable();
            $table->string('WTSum', 1)->nullable();
            $table->string('WTSumFC', 1)->nullable();
            $table->string('WTSumSC', 1)->nullable();
            $table->string('PayBlock', 1)->nullable();
            $table->string('PayBlckRef', 1)->nullable();
            $table->string('LicTradNum', 32)->nullable();
            $table->string('InterimTyp', 1)->nullable();
            $table->string('DprId', 1)->nullable();
            $table->string('MatchRef', 1)->nullable();
            $table->string('Ordered', 1)->nullable();
            $table->string('CUP', 1)->nullable();
            $table->string('CIG', 1)->nullable();
            $table->string('BPLId', 100)->nullable();
            $table->string('BPLName', 100)->nullable();
            $table->string('VatRegNum', 1)->nullable();
            $table->string('SLEDGERF', 1)->nullable();
            $table->string('InitRef2', 1)->nullable();
            $table->string('InitRef3Ln', 1)->nullable();
            $table->string('ExpUUID', 1)->nullable();
            $table->string('ExpOPType', 1)->nullable();
            $table->string('ExTransId', 1)->nullable();
            $table->string('DocArr', 1)->nullable();
            $table->string('DocLine', 1)->nullable();
            $table->string('MYFtype', 1)->nullable();
            $table->string('DocEntry', 1)->nullable();
            $table->string('DocNum', 1)->nullable();
            $table->string('DocType', 1)->nullable();
            $table->string('DocSubType', 1)->nullable();
            $table->string('RmrkTmpt', 1)->nullable();
            $table->string('CemCode', 1)->nullable();
            $table->string('CAOutCode', 1)->nullable();
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
        Schema::dropIfExists('j_d_t1_s');
    }
}
