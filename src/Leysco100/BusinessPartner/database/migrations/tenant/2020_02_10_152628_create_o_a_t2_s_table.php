<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOAT2STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_a_t2_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('AgrNo')
            ->references('id')->on('o_o_a_t_s')->nullable();
            $table->integer('AgrEfctNum')->nullable();
            $table->string('DatePeriod')->default('M');
            $table->date('FromDate')->nullable();
            $table->date('ToDate')->nullable();
            $table->string('CallUp', 100)->nullable();
            $table->integer('WhsCode')
            ->references('id')->on('warehouses')->nullable();
            $table->float('Quantity', 19, 6)->nullable();
            $table->string('ConsumeFCT', 1)->nullable();
            $table->string('FreeTxt', 100)->nullable();
            $table->integer('LogInstanc')->default(0);
            $table->float('AmountLC', 19, 6)->nullable();
            $table->float('AmountFC', 19, 6)->nullable();
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
        Schema::dropIfExists('o_a_t2_s');
    }
}
