<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOINMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_i_n_m_s', function (Blueprint $table) {
            $table->id();
            $table->integer('TransNum')->nullable();
            $table->integer('TransType')->nullable();
            $table->string('CreatedBy', 1)->nullable();
            $table->string('BASE_REF', 1)->nullable();
            $table->integer('DocLineNum')->nullable();
            $table->string('DocDate', 1)->nullable();
            $table->string('DocDueDate', 1)->nullable();
            $table->string('CardCode', 15)->nullable();
            $table->string('CardName', 100)->nullable();
            $table->string('Ref1', 1)->nullable();
            $table->string('Ref2', 1)->nullable();
            $table->string('Comments', 1)->nullable();
            $table->string('JrnlMemo', 1)->nullable();
            $table->string('DocTime', 1)->nullable();
            $table->string('ItemCode', 20)->nullable();
            $table->string('Dscription', 1)->nullable();
            $table->string('InQty', 1)->nullable();
            $table->string('OutQty', 1)->nullable();
            $table->string('Price', 1)->nullable();
            $table->string('Currency', 1)->nullable();
            $table->string('Rate', 1)->nullable();
            $table->string('VendorNum', 1)->nullable();
            $table->string('SerialNum', 1)->nullable();
            $table->string('Warehouse', 1)->nullable();
            $table->string('TreeType', 1)->nullable();
            $table->integer('SlpCode')->nullable();
            $table->string('TaxDate', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('PrjCode', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('BlockNum', 1)->nullable();
            $table->string('ImportLog', 1)->nullable();
            $table->string('UseDocPric', 1)->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('CalcPrice', 1)->nullable();
            $table->string('OpenQty', 1)->nullable();
            $table->string('Instance', 1)->nullable();
            $table->string('LastInst', 1)->nullable();
            $table->string('RevalTotal', 1)->nullable();
            $table->string('BaseCurr', 1)->nullable();
            $table->string('ApplObj', 1)->nullable();
            $table->string('AppObjAbs', 1)->nullable();
            $table->string('AppObjType', 1)->nullable();
            $table->string('StockAct', 1)->nullable();
            $table->string('TrnsfrAct', 1)->nullable();
            $table->string('PriceDifAc', 1)->nullable();
            $table->string('VarianceAc', 1)->nullable();
            $table->string('ReturnAct', 1)->nullable();
            $table->string('ExcRateAct', 1)->nullable();
            $table->string('ClearAct', 1)->nullable();
            $table->string('CostAct', 1)->nullable();
            $table->string('WipAct', 1)->nullable();
            $table->string('Balance', 1)->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->string('BaseLine', 1)->nullable();
            $table->string('TransValue', 1)->nullable();
            $table->string('PriceDiff', 1)->nullable();
            $table->string('TransSeq', 1)->nullable();
            $table->string('InvntAct', 1)->nullable();
            $table->string('RemMethod', 1)->nullable();
            $table->string('OpenValue', 1)->nullable();
            $table->string('SubLineNum', 1)->nullable();
            $table->string('AppObjLine', 1)->nullable();
            $table->string('Expenses', 1)->nullable();
            $table->string('OpenExp', 1)->nullable();
            $table->string('Allocation', 1)->nullable();
            $table->string('OpenAlloc', 1)->nullable();
            $table->string('ExpAlloc', 1)->nullable();
            $table->string('OExpAlloc', 1)->nullable();
            $table->string('OpenPDiff', 1)->nullable();
            $table->string('ExchDiff', 1)->nullable();
            $table->string('OpenEDiff', 1)->nullable();
            $table->string('NegInvAdjs', 1)->nullable();
            $table->string('OpenNegInv', 1)->nullable();
            $table->string('NegStckAct', 1)->nullable();
            $table->string('BTransVal', 1)->nullable();
            $table->string('VarVal', 1)->nullable();
            $table->string('BExpVal', 1)->nullable();
            $table->string('CogsVal', 1)->nullable();
            $table->string('BNegAVal', 1)->nullable();
            $table->string('IOffIncAcc', 1)->nullable();
            $table->string('IOffIncVal', 1)->nullable();
            $table->string('DOffDecAcc', 1)->nullable();
            $table->string('DOffDecVal', 1)->nullable();
            $table->string('DecAcc', 1)->nullable();
            $table->string('DecVal', 1)->nullable();
            $table->string('WipVal', 1)->nullable();
            $table->string('WipVarAcc', 1)->nullable();
            $table->string('WipVarVal', 1)->nullable();
            $table->string('IncAct', 1)->nullable();
            $table->string('IncVal', 1)->nullable();
            $table->string('ExpCAcc', 1)->nullable();
            $table->string('CostMethod', 1)->nullable();
            $table->string('OcrCode', 8)->nullable();
            $table->string('BaseQty', 1)->nullable();
            $table->string('PrevTrans', 1)->nullable();
            $table->string('HTransSeq', 1)->nullable();
            $table->string('OcrCode2', 8)->nullable();
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
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
        Schema::dropIfExists('o_i_n_m_s');
    }
}
