<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOJDTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_j_d_t_s', function (Blueprint $table) {
            $table->id();
            $table->integer('BatchNum')->nullable();
            $table->integer('TransId')->nullable();
            $table->string('BtfStatus', 1)->nullable();
            $table->string('TransType', 20)->nullable();
            $table->string('BaseRef', 100)->nullable();
            $table->date('RefDate')->nullable();
            $table->string('Memo', 1)->nullable();
            $table->string('Ref1')->nullable();
            $table->string('Ref2')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->float('LocTotal', 19, 6)->nullable();
            $table->string('FcTotal', 1)->nullable();
            $table->string('SysTotal', 1)->nullable();
            $table->string('TransCode', 1)->nullable();
            $table->string('OrignCurr', 1)->nullable();
            $table->string('TransRate', 1)->nullable();
            $table->string('BtfLine', 1)->nullable();
            $table->integer('TransCurr')->nullable();
            $table->string('Project', 1)->nullable();
            $table->date('DueDate')->nullable();
            $table->date('TaxDate')->nullable();
            $table->string('PCAddition', 1)->nullable();
            $table->string('FinncPriod', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->date('CreateDate')->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->string('RefndRprt', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('Indicator', 1)->nullable();
            $table->string('AdjTran', 1)->nullable();
            $table->string('RevSource', 1)->nullable();
            $table->string('StornoDate', 1)->nullable();
            $table->string('StornoToTr', 1)->nullable();
            $table->string('AutoStorno', 1)->nullable();
            $table->string('Corisptivi', 1)->nullable();
            $table->string('VatDate', 1)->nullable();
            $table->string('StampTax', 1)->nullable();
            $table->string('Series')->nullable();
            $table->string('Number', 1)->nullable();
            $table->string('AutoVAT', 1)->nullable();
            $table->string('DocSeries', 1)->nullable();
            $table->string('FolioPref', 1)->nullable();
            $table->string('FolioNum', 1)->nullable();
            $table->string('CreateTime', 1)->nullable();
            $table->string('BlockDunn', 1)->nullable();
            $table->string('ReportEU', 1)->nullable();
            $table->string('Report347', 1)->nullable();
            $table->string('Printed', 1)->nullable();
            $table->string('DocType', 1)->nullable();
            $table->string('AttNum', 1)->nullable();
            $table->string('GenRegNo', 1)->nullable();
            $table->string('RG23APart2', 1)->nullable();
            $table->string('RG23CPart2', 1)->nullable();
            $table->string('MatType', 1)->nullable();
            $table->string('Creator', 1)->nullable();
            $table->string('Approver', 1)->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('SeqCode', 1)->nullable();
            $table->string('Serial', 1)->nullable();
            $table->string('SeriesStr', 1)->nullable();
            $table->string('SubStr', 1)->nullable();
            $table->string('AutoWT', 1)->nullable();
            $table->string('WTSum', 1)->nullable();
            $table->string('WTSumSC', 1)->nullable();
            $table->string('WTSumFC', 1)->nullable();
            $table->string('WTApplied', 1)->nullable();
            $table->string('WTAppliedS', 1)->nullable();
            $table->string('WTAppliedF', 1)->nullable();
            $table->string('BaseAmnt', 1)->nullable();
            $table->string('BaseAmntSC', 1)->nullable();
            $table->string('BaseAmntFC', 1)->nullable();
            $table->string('BaseVtAt', 1)->nullable();
            $table->string('BaseVtAtSC', 1)->nullable();
            $table->string('BaseVtAtFC', 1)->nullable();
            $table->string('VersionNum', 1)->nullable();
            $table->string('BaseTrans', 1)->nullable();
            $table->string('ResidenNum', 1)->nullable();
            $table->string('OperatCode', 1)->nullable();
            $table->string('Ref3', 1)->nullable();
            $table->string('SSIExmpt', 1)->nullable();
            $table->string('SignMsg', 1)->nullable();
            $table->string('SignDigest', 1)->nullable();
            $table->string('CertifNum', 1)->nullable();
            $table->string('KeyVersion', 1)->nullable();
            $table->string('CUP', 1)->nullable();
            $table->string('CIG', 1)->nullable();
            $table->string('SupplCode', 1)->nullable();
            $table->string('SPSrcType', 1)->nullable();
            $table->string('SPSrcID', 1)->nullable();
            $table->string('SPSrcDLN', 1)->nullable();
            $table->string('DeferedTax', 1)->nullable();
            $table->string('AgrNo', 1)->nullable();
            $table->string('SeqNum', 1)->nullable();
            $table->string('ECDPosTyp', 1)->nullable();
            $table->string('RptPeriod', 1)->nullable();
            $table->string('RptMonth', 1)->nullable();
            $table->string('ExTransId', 1)->nullable();
            $table->string('PrlLinked', 1)->nullable();
            $table->string('PTICode', 1)->nullable();
            $table->string('Letter', 1)->nullable();
            $table->string('FolNumFrom', 1)->nullable();
            $table->string('FolNumTo', 1)->nullable();
            $table->string('RepSection', 1)->nullable();
            $table->string('ExclTaxRep', 1)->nullable();
            $table->string('IsCoEntry', 1)->nullable();
            $table->string('SAPPassprt', 1)->nullable();
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
        Schema::dropIfExists('o_j_d_t_s');
    }
}
