<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOITWSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_i_t_w_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ItemCode', 50)
                ->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->integer('ItemID')
                ->references('id')->on('o_i_t_m_s')->nullable();
            $table->string('WhsID')
                ->references('id')->on('o_w_h_s_s')->nullable();
            $table->string('WhsCode')
                ->references('WhsCode')->on('o_w_h_s_s')->nullable();
            $table->float('OnHand', 19, 6)->default(0);
            $table->float('IsCommited', 19, 6)->default(0);
            $table->float('OnOrder', 19, 6)->default(0);
            $table->float('Consig', 19, 6)->default(0);
            $table->float('Counted', 19, 6)->default(0);
            $table->string('WasCounted', 1)->nullable();
            $table->integer('UserSign')
                ->references('id')->on('users');
            $table->float('MinStock', 19, 6)->nullable();
            $table->float('MaxStock', 19, 6)->nullable();
            $table->float('MinOrder', 19, 6)->nullable();
            $table->float('AvgPrice', 19, 6)->default(0);
            $table->string('Locked', 1)->nullable();
            $table->integer('BalInvntAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('SaleCostAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('TransferAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('RevenuesAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('VarianceAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('DecreasAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('IncreasAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ReturnAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpensesAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('EURevenuAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('EUExpensAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FrRevenuAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FrExpensAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExmptIncom')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('PriceDifAc', 1)->nullable();
            $table->string('ExchangeAc', 1)->nullable();
            $table->string('BalanceAcc', 1)->nullable();
            $table->string('PurchaseAc', 1)->nullable();
            $table->string('PAReturnAc', 1)->nullable();
            $table->string('PurchOfsAc', 1)->nullable();
            $table->string('ShpdGdsAct', 1)->nullable();
            $table->string('VatRevAct', 1)->nullable();
            $table->string('StockValue', 1)->nullable();
            $table->string('DecresGlAc', 1)->nullable();
            $table->string('IncresGlAc', 1)->nullable();
            $table->string('StokRvlAct', 1)->nullable();
            $table->string('StkOffsAct', 1)->nullable();
            $table->string('WipAcct', 1)->nullable();
            $table->string('WipVarAcct', 1)->nullable();
            $table->string('CostRvlAct', 1)->nullable();
            $table->string('CstOffsAct', 1)->nullable();
            $table->string('ExpClrAct', 1)->nullable();
            $table->string('ExpOfstAct', 1)->nullable();
            $table->string('Object', 1)->nullable();
            $table->string('logInstanc', 1)->nullable();
            $table->string('createDate', 1)->nullable();
            $table->string('userSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('ARCMAct', 1)->nullable();
            $table->string('ARCMFrnAct', 1)->nullable();
            $table->string('ARCMEUAct', 1)->nullable();
            $table->string('ARCMExpAct', 1)->nullable();
            $table->string('APCMAct', 1)->nullable();
            $table->string('APCMFrnAct', 1)->nullable();
            $table->string('APCMEUAct', 1)->nullable();
            $table->string('RevRetAct', 1)->nullable();
            $table->string('NegStckAct', 1)->nullable();
            $table->string('StkInTnAct', 1)->nullable();
            $table->string('PurBalAct', 1)->nullable();
            $table->string('WhICenAct', 1)->nullable();
            $table->string('WhOCenAct', 1)->nullable();
            $table->string('WipOffset', 1)->nullable();
            $table->string('StockOffst', 1)->nullable();
            $table->string('DftBinAbs', 1)->nullable();
            $table->string('DftBinEnfd', 1)->nullable();
            $table->string('Freezed', 1)->nullable();
            $table->string('FreezeDoc', 1)->nullable();
            $table->string('FreeChrgSA', 1)->nullable();
            $table->string('FreeChrgPU', 1)->nullable();
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
        Schema::dropIfExists('o_i_t_w_s');
    }
}
