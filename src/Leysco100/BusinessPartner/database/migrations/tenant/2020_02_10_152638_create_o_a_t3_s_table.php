<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOAT3STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_a_t3_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('AgrNo')
                ->references('id')->on('o_o_a_t_s')->nullable();
            $table->integer('AgrLnNum')->nullable();
            $table->integer('AgrEfctNum')->nullable();
            $table->integer('ActivityID')->nullable();
            $table->integer('LogInstanc')->nullable();
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
        Schema::dropIfExists('o_a_t3_s');
    }
}
