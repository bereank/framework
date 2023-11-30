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
        Schema::create('o_i_l_m_s', function (Blueprint $table) {
            $table->id();
            $table->Integer('MessageID')->nullable();
            $table->Integer('DocEntry')->nullable();
            $table->Integer('TransType')->nullable();
            $table->Integer('DocLineNum')->nullable();
            $table->decimal('Quantity', 19, 6)->nullable();
            $table->decimal('EffectQty', 19, 6)->nullable();
            $table->Integer('LocType')->nullable();
            $table->string('LocCode', 8)->nullable();
            $table->decimal('TotalLC', 19, 6)->nullable();
            $table->decimal('TotalFC', 19, 6)->nullable();
            $table->decimal('TotalSC', 19, 6)->nullable();
            $table->Integer('BaseAbsEnt')->nullable();
            $table->Integer('BaseType')->nullable();
            $table->string('BaseCurr', 3)->nullable();
            $table->string('Currency', 3)->nullable();
            $table->Integer('AccumType')->nullable();
            $table->Integer('ActionType')->nullable();
            $table->decimal('ExpensesLC', 19, 6)->nullable();
            $table->decimal('ExpensesFC', 19, 6)->nullable();
            $table->decimal('ExpensesSC', 19, 6)->nullable();
            $table->date('DocDueDate')->nullable();
            $table->string('ItemCode', 50)->nullable();
            $table->string('BPCardCode', 15)->nullable();
            $table->date('DocDate')->nullable();
            $table->decimal('DocRate', 19, 6)->nullable();
            $table->string('Comment', 254)->nullable();
            $table->string('JrnlMemo', 50)->nullable();
            $table->string('Ref1', 11)->nullable();
            $table->string('Ref2', 100)->nullable();
            $table->Integer('BaseLine')->nullable();
            $table->Integer('SnBType')->nullable();
            $table->Integer('CreateTime')->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->date('CreateDate')->nullable();
            $table->string('OcrCode', 8)->nullable();
            $table->string('OcrCode2', 8)->nullable();
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
            $table->decimal('DocPrice', 19, 6)->nullable();
            $table->string('CardName', 100)->nullable();
            $table->string('Dscription', 100)->nullable();
            $table->string('TreeType', 1)->nullable();
            $table->Integer('ApplObj')->nullable();
            $table->Integer('AppObjAbs')->nullable();
            $table->string('AppObjType', 1)->nullable();
            $table->Integer('AppObjLine')->nullable();
            $table->string('BASE_REF', 11)->nullable();
            $table->Integer('TransSeqRf')->nullable();
            $table->Integer('LayerIDRef')->nullable();
            $table->string('VersionNum', 11)->nullable();
            $table->decimal('PriceRate', 19, 6)->nullable();
            $table->string('PriceCurr', 3)->nullable();
            $table->decimal('DocTotal', 19, 6)->nullable();
            $table->decimal('Price', 19, 6)->nullable();
            $table->decimal('CIShbQty', 19, 6)->nullable();
            $table->Integer('SubLineNum')->nullable();
            $table->string('PrjCode', 20)->nullable();
            $table->Integer('SlpCode')->nullable();
            $table->date('TaxDate')->nullable();
            $table->string('UseDocPric', 1)->nullable();
            $table->string('VendorNum', 50)->nullable();
            $table->string('SerialNum', 17)->nullable();
            $table->string('BlockNum', 100)->nullable();
            $table->string('ImportLog', 20)->nullable();
            $table->Integer('Location')->nullable();
            $table->decimal('DocPrcRate', 19, 6)->nullable();
            $table->string('DocPrcCurr', 3)->nullable();
            $table->string('CgsOcrCod', 8)->nullable();
            $table->string('CgsOcrCod2', 8)->nullable();
            $table->string('CgsOcrCod3', 8)->nullable();
            $table->string('CgsOcrCod4', 8)->nullable();
            $table->string('CgsOcrCod5', 8)->nullable();
            $table->Integer('BSubLineNo')->nullable();
            $table->Integer('AppSubLine')->nullable();
            $table->Integer('UserSign')->nullable();
            $table->decimal('SysRate', 19, 6)->nullable();
            $table->string('ExFromRpt', 1)->nullable();
            $table->string('Ref3', 11)->nullable();
            $table->string('EnSetCost', 1)->nullable();
            $table->decimal('RetCost', 19, 6)->nullable();
            $table->Integer('DocAction')->nullable();
            $table->string('UseShpdGd', 1)->nullable();
            $table->decimal('AddTotalLC', 19, 6)->nullable();
            $table->decimal('AddExpLC', 19, 6)->nullable();
            $table->string('IsNegLnQty', 1)->nullable();
            $table->Integer('StgSeqNum')->nullable();
            $table->Integer('StgEntry')->nullable();
            $table->string('StgDesc', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_i_l_m_s');
    }
};
