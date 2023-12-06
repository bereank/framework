<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOAT1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_a_t1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AgrNo')
                ->references('id')->on('o_o_a_t_s');
            $table->string('ItemCode', 50)->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->integer('ItemID')->references('id')->on('o_i_t_m_s')->nullable();
            $table->string('ItemName', 150)->nullable();
            $table->string('ItemGroup', 1)->nullable();
            $table->float('PlanQty', 19, 6)->nullable();
            $table->float('UnitPrice', 19, 6)->nullable();
            $table->string('Currency', 1)->nullable();
            $table->float('CumQty', 19, 6)->nullable();
            $table->float('CumAmntFC', 19, 6)->nullable();
            $table->float('CumAmntLC', 19, 6)->nullable();
            $table->string('FreeTxt', 100)->nullable();
            $table->string('InvntryUom', 100)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('VisOrder', 1)->nullable();
            $table->float('RetPortion', 19, 6)->nullable();
            $table->date('WrrtyEnd')->nullable();
            $table->string('LineStatus', 1)->default('O');
            $table->float('PlanAmtLC', 19, 6)->nullable();
            $table->float('PlanAmtFC', 19, 6)->nullable();
            $table->float('Discount', 19, 6)->nullable();
            $table->integer('UomEntry')
                ->references('id')->on('o_i_t_m_s');
            $table->string('UomCode', 20)->nullable();
            $table->integer('NumPerMsr')->nullable();
            $table->string('UndlvQty', 1)->nullable();
            $table->string('UndlvAmntL', 1)->nullable();
            $table->string('UndlvAmntF', 1)->nullable();
            $table->string('TrnspCode', 1)->nullable();
            $table->string('Project', 1)->nullable();
            $table->string('TaxCode', 1)->nullable();
            $table->float('TAXRate', 19, 6)->nullable();
            $table->float('PlVatAmtLC', 19, 6)->nullable();
            $table->float('PlVatAmtFC', 19, 6)->nullable();
            $table->float('CumVtAmtLC', 19, 6)->nullable();
            $table->float('CumVtAmtFC', 19, 6)->nullable();

            //User Defined Fields
            $table->float('U_PricePerPricingUnit', 19, 6)->nullable();
            $table->float('U_Sqrft', 19, 6)->nullable();
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
        Schema::dropIfExists('o_a_t1_s');
    }
}
