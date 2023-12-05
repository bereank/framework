<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AcctCode', 15);
            $table->string('AcctName', 100);
            $table->float('CurrTotal', 19, 6)->default(0);
            $table->float('EndTotal', 19, 6)->default(0);
            $table->string('Finanse', 1)->default('N');
            $table->string('Groups', 8)->nullable();
            $table->string('Budget', 1)->default('N');
            $table->string('Frozen', 1)->default('N');
            $table->string('Free_2', 1)->nullable();
            $table->string('Postable', 1)->default('Y');
            $table->string('Fixed', 1)->nullable();
            $table->integer('Levels')->nullable();
            $table->string('ExportCode', 10)->nullable();
            $table->integer('GrpLine')->nullable();
            $table->integer('chart_of_account_id')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('AccntntCod', 15)->nullable();
            $table->string('CashBox', 1)->default('N');
            $table->integer('GroupMask')->nullable();
            $table->string('RateTrans', 1)->default('Y');
            $table->string('TaxIncome', 1)->default('N');
            $table->string('ExmIncome', 1)->default('N');
            $table->integer('ExtrMatch')->nullable();
            $table->integer('IntrMatch')->nullable();
            $table->string('ActType', 1)->default('N');
            $table->string('Transfered', 1)->default('N');
            $table->string('BlncTrnsfr', 1)->default('N');
            $table->string('OverType', 1)->default('N');
            $table->string('OverCode', 8)->nullable();
            $table->string('SysMatch', 1)->nullable();
            $table->string('PrevYear', 1)->default('N');
            $table->integer('ActCurr')
                ->references('id')->on('currencies')->nullable();
            $table->string('RateDifAct', 15)->nullable();
            $table->float('SysTotal', 19, 6)->default(0.00);
            $table->float('FcTotal', 19, 6)->default(0.00);
            $table->string('Protected', 1)->default('N');
            $table->string('RealAcct', 1)->default('N');
            $table->string('Advance', 1)->default('Y');
            $table->string('FrgnName', 100)->nullable();
            $table->string('Details', 254)->nullable();
            $table->float('ExtraSum', 19, 6)->nullable();
            $table->string('Project', 20)->nullable();
            $table->string('RevalMatch', 1)->default('N');
            $table->string('DataSource', 1)->default('N');
            $table->string('LocMth', 1)->default('Y');
            $table->integer('MTHCounter')->nullable();
            $table->integer('BNKCounter')->nullable();
            $table->string('UserSign', 1)
                ->references('id')->on('users')->default(1);
            $table->string('LocManTran', 1)->default('N');
            $table->integer('LogInstanc')->nullable();
            $table->string('ObjType', 20)->default(1);
            $table->string('ValidFor', 1)->default('N');
            $table->string('ValidFrom', 15)->nullable();
            $table->string('ValidTo', 15)->nullable();
            $table->string('ValidComm', 1)->nullable();
            $table->string('FrozenFor', 1)->default('N');
            $table->string('FrozenFrom', 15)->nullable();
            $table->string('FrozenTo', 15)->nullable();
            $table->string('FrozenComm', 30)->nullable();
            $table->string('Counter', 1)->nullable();
            $table->string('Segment_0', 20)->nullable();
            $table->string('Segment_1', 20)->nullable();
            $table->string('Segment_2', 20)->nullable();
            $table->string('Segment_3', 20)->nullable();
            $table->string('Segment_4', 20)->nullable();
            $table->string('Segment_5', 20)->nullable();
            $table->string('Segment_6', 20)->nullable();
            $table->string('Segment_7', 20)->nullable();
            $table->string('Segment_8', 20)->nullable();
            $table->string('Segment_9', 20)->nullable();
            $table->string('FormatCode', 210)->nullable();
            $table->string('CfwRlvnt', 1)->default('N');
            $table->string('ExchRate', 1)->default('N');
            $table->string('RevalAcct', 1)->nullable();
            $table->float('LastRevBal', 19, 6)->default(0.00);
            $table->string('LastRevDat', 8)->nullable();
            $table->string('DfltVat', 1)
                ->references('id')->on('tax_groups')->default(1);
            $table->string('VatChange', 1)->nullable();
            $table->string('Category', 1)->nullable();
            $table->string('TransCode', 1)->nullable();
            $table->string('OverCode5', 1)->nullable();
            $table->string('OverCode2', 1)->nullable();
            $table->string('OverCode3', 1)->nullable();
            $table->string('OverCode4', 1)->nullable();
            $table->string('DfltTax', 1)->nullable();
            $table->string('TaxPostAcc', 1)->nullable();
            $table->string('AcctStrLe', 1)->nullable();
            $table->string('MeaUnit', 1)->nullable();
            $table->string('BalDirect', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->string('PlngLevel', 1)->nullable();
            $table->string('MultiLink', 1)->nullable();
            $table->string('PrjRelvnt', 1)->nullable();
            $table->string('Dim1Relvnt', 1)->nullable();
            $table->string('Dim2Relvnt', 1)->nullable();
            $table->string('Dim3Relvnt', 1)->nullable();
            $table->string('Dim4Relvnt', 1)->nullable();
            $table->string('Dim5Relvnt', 1)->nullable();
            $table->string('AccrualTyp', 1)->nullable();
            $table->string('DatevAcct', 1)->nullable();
            $table->string('DatevAutoA', 1)->nullable();
            $table->string('DatevFirst', 1)->nullable();
            $table->string('SnapShotId', 1)->nullable();
            $table->string('PCN874Rpt', 1)->nullable();
            $table->string('SCAdjust', 1)->nullable();
            $table->string('BPLId', 100)->nullable();
            $table->string('BPLName', 100)->nullable();
            $table->string('SubLedgerN', 1)->nullable();
            $table->string('VATRegNum', 1)->nullable();
            $table->string('ActId', 1)->nullable();
            $table->string('ClosingAcc', 1)->nullable();
            $table->string('PurpCode', 1)->nullable();
            $table->string('RefCode', 1)->nullable();
            $table->string('BlocManPos', 1)->nullable();
            $table->string('PriAccCode', 1)->nullable();
            $table->string('CstAccOnly', 1)->nullable();
            $table->string('AlloweFrom', 1)->nullable();
            $table->string('AllowedTo', 1)->nullable();
            $table->string('BalanceA', 1)->nullable();
            $table->string('RmrkTmpt', 1)->nullable();
            $table->string('CemRelvnt', 1)->nullable();
            $table->string('CemCode', 1)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
