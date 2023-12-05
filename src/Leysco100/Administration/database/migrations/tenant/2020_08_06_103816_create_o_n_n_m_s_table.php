<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateONNMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_n_n_m_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ObjectCode')->nullable();
            $table->integer('AutoKey')->nullable();
            $table->integer('DfltSeries')->nullable(); //Default Series
            $table->integer('UpdCounter')->nullable(); //Update Counter
            $table->integer('UserSign')->nullable();
            $table->string('DocSubType', 2)->nullable(); //	DocSubType
            $table->string('DocAlias', 20)->nullable(); //DocAlias
            $table->string('PeriodTyp', 1)->nullable();
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
        Schema::dropIfExists('o_n_n_m_s');
    }
}
