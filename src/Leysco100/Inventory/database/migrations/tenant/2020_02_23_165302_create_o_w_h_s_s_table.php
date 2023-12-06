<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOWHSSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_w_h_s_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('WhsCode', 50)->unique();
            $table->string('WhsName', 100)->nullable();
            $table->string('Grp_Code', 1)->nullable();
            $table->integer('BalInvntAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('SaleCostAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('TransferAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('Locked', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
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
            $table->string('VatGroup', 1)->nullable();
            $table->string('Street', 1)->nullable();
            $table->string('Block', 1)->nullable();
            $table->string('ZipCode', 1)->nullable();
            $table->string('City', 1)->nullable();
            $table->string('County', 1)->nullable();
            $table->string('Country', 1)->nullable();
            $table->string('State', 1)->nullable();
            $table->string('Location', 8)->nullable();
            $table->string('DropShip', 1)->nullable();
            $table->string('ExmptIncom', 1)->nullable();
            $table->string('UseTax', 1)->nullable();

            $table->integer('PriceDifAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExchangeAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('BalanceAcc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PurchaseAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PAReturnAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PurchOfsAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FedTaxID')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('Building')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ShpdGdsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('VatRevAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('DecresGlAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('IncresGlAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('Nettable')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StokRvlAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StkOffsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('WipAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('WipVarAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('CostRvlAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('CstOffsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpClrAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpOfstAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('objType', 1)->nullable();
            $table->string('logInstanc', 1)->nullable();
            $table->string('createDate', 1)->nullable();
            $table->string('userSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->integer('ARCMAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMFrnAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMEUAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMExpAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMFrnAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMEUAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('RevRetAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('BPLId')->nullable();
            $table->integer('OwnerCode')->nullable();
            $table->integer('NegStckAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StkInTnAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('AddrType', 1)->nullable();
            $table->string('StreetNo', 1)->nullable();
            $table->string('PurBalAct', 1)->nullable();
            $table->string('Excisable', 1)->nullable();
            $table->string('WhICenAct', 1)->nullable();
            $table->string('WhOCenAct', 1)->nullable();
            $table->string('WhShipTo', 1)->nullable();
            $table->string('WipOffset', 1)->nullable();
            $table->string('StockOffst', 1)->nullable();
            $table->string('StorKeeper', 1)->nullable();
            $table->string('Shipper', 1)->nullable();
            $table->string('BinActivat', 1)->nullable();
            $table->string('BinSeptor', 1)->nullable();
            $table->string('DftBinAbs', 1)->nullable();
            $table->string('DftBinEnfd', 1)->nullable();
            $table->string('AutoIssMtd', 1)->nullable();
            $table->string('ManageSnB', 1)->nullable();
            $table->string('RecItemsBy', 1)->nullable();
            $table->string('RecBinEnab', 1)->nullable();
            $table->string('GlblLocNum', 1)->nullable();
            $table->string('RecvEmpBin', 1)->nullable();
            $table->string('Inactive', 1)->nullable();
            $table->string('RecvMaxQty', 1)->nullable();
            $table->string('AutoRecvMd', 1)->nullable();
            $table->string('RecvMaxWT', 1)->nullable();
            $table->string('RecvUpTo', 1)->nullable();
            $table->string('FreeChrgSA', 1)->nullable();
            $table->string('FreeChrgPU', 1)->nullable();
            $table->string('TaxOffice', 1)->nullable();
            $table->string('Address2', 1)->nullable();
            $table->string('Address3', 1)->nullable();
            $table->string('External', 1)->nullable();
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
        Schema::dropIfExists('o_w_h_s_s');
    }
}
