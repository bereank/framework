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
        Schema::create('p_d_f2_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('DocNum')
                ->references('id')->on('o_p_d_f_s')->nullable();
            $table->integer('InvoiceId')->nullable();
            $table->integer('InvoiceDraftKey')->nullable();
            $table->integer('DocEntry')->nullable();
            $table->float('SumApplied', 19, 6)->nullable();
            $table->float('AppliedFC', 19, 6)->nullable();
            $table->float('AppliedSys', 19, 6)->nullable();
            $table->integer('InvType')->default(13);
            $table->float('DocRate', 19, 6)->nullable();
            $table->integer('Flags')->default(0);
            $table->string('IntrsStat', 1)->nullable();
            $table->string('DocLine', 1)->nullable();
            $table->float('vatApplied', 19, 6)->nullable();
            $table->float('vatAppldFC', 19, 6)->nullable();
            $table->float('vatAppldSy', 19, 6)->nullable();
            $table->string('selfInv', 1)->default('N');
            $table->string('ObjType', 20)->default(24);
            $table->string('LogInstanc', 1)->nullable();
            $table->float('Dcount', 19, 6)->nullable();
            $table->float('DcntSum', 19, 6)->nullable();
            $table->float('DcntSumFC', 19, 6)->nullable();
            $table->float('DcntSumSy', 19, 6)->nullable();
            $table->float('BfDcntSum', 19, 6)->nullable();
            $table->float('BfDcntSumF', 19, 6)->nullable();
            $table->float('BfDcntSumS', 19, 6)->nullable();
            $table->float('BfNetDcnt', 19, 6)->nullable();
            $table->float('BfNetDcntF', 19, 6)->nullable();
            $table->float('BfNetDcntS', 19, 6)->nullable();
            $table->float('PaidSum', 19, 6)->nullable();
            $table->float('ExpAppld', 19, 6)->nullable();
            $table->float('ExpAppldFC', 19, 6)->nullable();
            $table->float('ExpAppldSC', 19, 6)->nullable();
            $table->float('Rounddiff', 19, 6)->nullable();
            $table->float('RounddifFc', 19, 6)->nullable();
            $table->float('RounddifSc', 19, 6)->nullable();
            $table->string('InstId', 1)->nullable();
            $table->float('WtAppld', 19, 6)->nullable();
            $table->float('WtAppldFC', 19, 6)->nullable();
            $table->float('WtAppldSC', 19, 6)->nullable();
            $table->string('LinkDate', 1)->nullable();
            $table->string('AmtDifPst', 1)->nullable();
            $table->string('PaidDpm', 1)->nullable();
            $table->string('DpmPosted', 1)->nullable();
            $table->string('ExpVatSum', 1)->nullable();
            $table->string('ExpVatSumF', 1)->nullable();
            $table->string('ExpVatSumS', 1)->nullable();
            $table->string('IsRateDiff', 1)->nullable();
            $table->string('WtInvCatS', 1)->nullable();
            $table->string('WtInvCatSF', 1)->nullable();
            $table->string('WtInvCatSS', 1)->nullable();
            $table->string('OcrCode', 8)->nullable();
            $table->string('DocTransId', 1)->nullable();
            $table->string('MIEntry', 1)->nullable();
            $table->string('OcrCode2', 8)->nullable()->comment("Costing Center 2");
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
            $table->string('IsSelected', 1)->nullable();
            $table->string('WTOnHold', 1)->nullable();
            $table->string('WTOnhldPst', 1)->nullable();
            $table->string('baseAbs', 1)->nullable();
            $table->string('MIType', 1)->nullable();
            $table->string('DocSubType', 1)->nullable();
            $table->string('SpltPmtVAT', 1)->nullable();
            $table->string('ExtRef')->nullable()->comment("External Ref");
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
        Schema::dropIfExists('p_d_f2_s');
    }
};
