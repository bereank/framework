<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITM1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_t_m1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ItemCode', 50)->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->integer('ItemID')->references('id')->on('o_i_t_m_s')->nullable();
            $table->integer('PriceList')->nullable();
            $table->float('Price', 19, 6)->nullable();
            $table->integer('Currency')->nullable();
            $table->string('Ovrwritten', 1)->nullable();
            $table->string('Factor', 1)->nullable();
            $table->string('CurrencyType', 20)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('AddPrice1', 1)->nullable();
            $table->integer('Currency1')->nullable();
            $table->string('AddPrice2', 1)->nullable();
            $table->integer('Currency2')->nullable();
            $table->string('Ovrwrite1', 1)->default('N');
            $table->string('Ovrwrite2', 1)->default('N');
            $table->integer('BasePLNum')->nullable();
            $table->integer('UomEntry')->nullable();
            $table->string('PriceType', 1)->nullable();
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
        Schema::dropIfExists('i_t_m1_s');
    }
}
