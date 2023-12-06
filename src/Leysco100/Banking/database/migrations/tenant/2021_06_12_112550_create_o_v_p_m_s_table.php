<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOVPMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_v_p_m_s', function (Blueprint $table) {
            $table->id();
            $table->integer('DocNum')->nullable();
            $table->string('DocType', 15)->default('C');
            $table->string('Canceled', 1)->default('N');
            $table->string('Handwrtten', 1)->default('N');
            $table->string('Printed', 1)->default('N');
            $table->date('DocDate', 50)->default(\Carbon\Carbon::now());
            $table->date('DocDueDate', 50)->default(\Carbon\Carbon::now());
            $table->string('CardCode', 50)
                ->references('CardCode')->on('o_c_r_d_s')->nullable();
            $table->string('CardName', 100)->nullable();
            $table->string('Address', 254)->nullable();
            $table->float('DdctPrcnt', 19, 6)->nullable();
            $table->float('DdctSum', 19, 6)->nullable();
            $table->float('DdctSumFC', 19, 6)->nullable();
            $table->integer('CashAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->float('CashSum', 19, 6)->nullable();
            $table->float('CashSumFC', 19, 6)->nullable();
            $table->float('CreditSum', 19, 6)->nullable();
            $table->float('CredSumFC', 19, 6)->nullable();
            $table->integer('CheckAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->float('CheckSum', 19, 6)->nullable();
            $table->string('CheckSumFC', 1)->nullable();
            $table->integer('TrsfrAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->float('TrsfrSum', 19, 6)->nullable();
            $table->string('TrsfrSumFC', 1)->nullable();
            $table->string('TrsfrDate', 50)->nullable();
            $table->string('TrsfrRef', 27)->nullable();
            $table->string('PayNoDoc', 1)->nullable();
            $table->float('NoDoScSum', 19, 6)->nullable();
            $table->float('NoDocSumFC', 19, 6)->nullable();
            $table->string('DocCurr', 1)->nullable();
            $table->string('DiffCurr', 1)->default('N');
            $table->float('DocRate', 19, 6)->nullable();
            $table->float('SysRate', 19, 6)->nullable();
            $table->float('DocTotal', 19, 6)->nullable();
            $table->float('DocTotalFC', 19, 6)->nullable();
            $table->string('Ref1', 254)->nullable();
            $table->string('Ref2', 254)->nullable();
            $table->string('CounterRef')->nullable();
            $table->string('Comments', 254)->nullable();
            $table->string('JrnlMemo', 1)->nullable();
            $table->integer('TransId')->nullable();
            $table->string('DocTime', 1)->nullable();
            $table->string('ShowAtCard', 1)->nullable();
            $table->string('SpiltTrans', 1)->nullable();
            $table->string('CreateTran', 1)->nullable();
            $table->string('Flags', 1)->nullable();
            $table->string('CntctCode', 1)->nullable();
            $table->string('DdctSumSy', 1)->nullable();
            $table->float('CashSumSy', 19, 6)->nullable();
            $table->float('CredSumSy', 19, 6)->nullable();
            $table->float('CheckSumSy', 19, 6)->nullable();
            $table->float('TrsfrSumSy', 19, 6)->nullable();
            $table->float('NoDocSumSy', 19, 6)->nullable();
            $table->float('DocTotalSy', 19, 6)->nullable();
            $table->integer('ObjType')->default(24);
            $table->string('StornoRate', 1)->nullable();
            $table->string('ApplyVAT', 1)->nullable();
            $table->date('TaxDate', 50)->nullable();
            $table->integer('Series')->nullable();
            $table->string('confirmed', 1)->default('N');
            $table->string('ShowJDT', 1)->default('N');
            $table->string('BankCode', 50)->nullable();
            $table->string('BankAcct', 50)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->integer('LogInstanc')->default(0);
            $table->float('VatGroup', 19, 6)->nullable();
            $table->float('VatSum', 19, 6)->nullable();
            $table->float('VatSumFC', 19, 6)->nullable();
            $table->float('VatSumSy', 19, 6)->nullable();
            $table->float('FinncPriod', 19, 6)->nullable();
            $table->float('VatPrcnt', 19, 6)->nullable();
            $table->float('Dcount', 19, 6)->nullable();
            $table->float('DcntSum', 19, 6)->nullable();
            $table->float('DcntSumFC', 19, 6)->nullable();
            $table->float('DcntSumSy', 19, 6)->nullable();
            $table->string('SpltCredLn', 1)->nullable();
            $table->string('PrjCode', 1)->nullable();
            $table->string('PaymentRef', 1)->nullable();
            $table->string('Submitted', 1)->nullable();
            $table->string('Status', 1)->nullable();
            $table->string('PayMth', 1)->nullable();
            $table->string('BankCountr', 1)->nullable();
            $table->string('FreightSum', 1)->nullable();
            $table->string('FreigtFC', 1)->nullable();
            $table->string('FreigtSC', 1)->nullable();
            $table->string('BoeAcc', 1)->nullable();
            $table->string('BoeNum', 1)->nullable();
            $table->string('BoeSum', 1)->nullable();
            $table->string('BoeSumFc', 1)->nullable();
            $table->string('BoeSumSc', 1)->nullable();
            $table->string('BoeAgent', 1)->nullable();
            $table->string('BoeStatus', 1)->nullable();
            $table->string('WtCode', 1)->nullable();
            $table->string('WtSum', 1)->nullable();
            $table->string('WtSumFrgn', 1)->nullable();
            $table->string('WtSumSys', 1)->nullable();
            $table->string('WtAccount', 1)->nullable();
            $table->string('WtBaseAmnt', 1)->nullable();
            $table->string('Proforma', 1)->nullable();
            $table->string('BoeAbs', 1)->nullable();
            $table->string('BpAct', 1)->nullable();
            $table->string('BcgSum', 1)->nullable();
            $table->string('BcgSumFC', 1)->nullable();
            $table->string('BcgSumSy', 1)->nullable();
            $table->string('PIndicator', 1)->nullable();
            $table->string('PaPriority', 1)->nullable();
            $table->string('PayToCode', 1)->nullable();
            $table->string('IsPaytoBnk', 1)->nullable();
            $table->string('PBnkCnt', 1)->nullable();
            $table->string('PBnkCode', 1)->nullable();
            $table->string('PBnkAccnt', 1)->nullable();
            $table->string('PBnkBranch', 1)->nullable();
            $table->string('WizDunBlck', 1)->nullable();
            $table->string('WtBaseSum', 1)->nullable();
            $table->string('WtBaseSumF', 1)->nullable();
            $table->string('WtBaseSumS', 1)->nullable();
            $table->string('UndOvDiff', 1)->nullable();
            $table->string('UndOvDiffS', 1)->nullable();
            $table->string('BankActKey', 1)->nullable();
            $table->string('VersionNum', 1)->nullable();
            $table->date('VatDate', 1)->nullable();
            $table->string('TransCode', 1)->nullable();
            $table->string('PaymType', 1)->default('N');
            $table->string('TfrRealAmt', 1)->nullable();
            $table->date('CancelDate', 50)->nullable();
            $table->float('OpenBal', 19, 6)->nullable();
            $table->float('OpenBalFc', 19, 6)->nullable();
            $table->float('OpenBalSc', 19, 6)->nullable();
            $table->float('BcgTaxSum', 19, 6)->nullable();
            $table->float('BcgTaxSumF', 19, 6)->nullable();
            $table->float('BcgTaxSumS', 19, 6)->nullable();
            $table->string('TpwID', 1)->nullable();
            $table->string('ChallanNo', 1)->nullable();
            $table->string('ChallanBak', 1)->nullable();
            $table->string('ChallanDat', 1)->nullable();
            $table->string('WddStatus', 1)->nullable();
            $table->string('BcgVatGrp', 1)->nullable();
            $table->string('BcgVatPcnt', 1)->nullable();
            $table->string('SeqCode', 1)->nullable();
            $table->string('Serial', 1)->nullable();
            $table->string('SeriesStr', 1)->nullable();
            $table->string('SubStr', 1)->nullable();
            $table->string('BSRCode', 1)->nullable();
            $table->string('LocCode', 1)->nullable();
            $table->string('WTOnhldPst', 1)->nullable();
            $table->integer('UserSign2')
                ->references('id')->on('users')->nullable();
            $table->string('BuildDesc', 1)->nullable();
            $table->string('ResidenNum', 1)->nullable();
            $table->string('OperatCode', 1)->nullable();
            $table->string('UndOvDiffF', 1)->nullable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
            $table->integer('BaseType')->nullable();
            $table->integer('BaseEntry')->nullable();
            $table->string('BPLId', 100)->nullable();
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
        Schema::dropIfExists('o_v_p_m_s');
    }
}
