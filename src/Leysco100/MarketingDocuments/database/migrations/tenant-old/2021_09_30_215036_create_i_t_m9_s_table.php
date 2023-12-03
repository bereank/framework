<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITM9STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_t_m9_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode', 50)->nullable();
            $table->integer('PriceList')->nullable();
            $table->integer('UomEntry')->nullable();
            $table->float('Factor', 19, 2)->nullable();
            $table->float('Price', 19, 2)->nullable();
            $table->string('Currency', 1)->nullable();
            $table->string('AutoUpdate', 1)->default("N");
            $table->string('AddPrice1', 1)->nullable();
            $table->string('Currency1', 1)->nullable();
            $table->string('AddPrice2', 1)->nullable();
            $table->string('Currency2', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->string('Factor1', 1)->nullable();
            $table->string('Factor2', 1)->nullable();
            $table->string('UpdateDate', 1)->nullable();
            $table->string('PriceType', 1)->nullable();
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
        Schema::dropIfExists('i_t_m9_s');
    }
}
