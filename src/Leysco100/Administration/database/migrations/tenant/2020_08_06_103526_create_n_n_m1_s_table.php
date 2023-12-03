<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNNM1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('n_n_m1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ObjectCode')->nullable();
            $table->integer('Series')->nullable();
            $table->string('SeriesName', 20)->nullable();
            $table->integer('InitialNum')->nullable();
            $table->integer('NextNumber')->nullable();
            $table->integer('LastNum')->nullable();
            $table->string('BeginStr', 20)->nullable();
            $table->string('EndStr', 20)->nullable();
            $table->string('Remark', 20)->nullable();
            $table->integer('GroupCode')->nullable(); //10=Series Group 10, 1=Series Group 1, 2=Series Group 2, 3=Series Group 3, 4=Series Group 4, 5=Series Group 5, 6=Series Group 6, 7=Series Group 7, 8=Series Group 8, 9=Series Group 9
            $table->string('Locked', 1)->default('N');
            $table->string('YearTransf', 1)->nullable();
            $table->string('Indicator', 1)->nullable();
            $table->string('Template', 1)->nullable();
            $table->string('NumSize', 1)->nullable();
            $table->string('FolioPref', 1)->nullable();
            $table->string('NextFolio', 1)->nullable();
            $table->string('DocSubType', 1)->nullable();
            $table->string('DefESeries', 1)->nullable();
            $table->string('IsDigSerie', 1)->nullable();
            $table->string('SeriesType', 1)->nullable();
            $table->string('IsManual', 1)->nullable();
            $table->string('BPLId', 1)->nullable();
            $table->string('IsForCncl', 1)->default('N');
            $table->string('AtDocType', 1)->nullable();
            $table->string('IsElAuth', 1)->nullable();
            $table->string('CoAccount', 1)->nullable();
            $table->string('ExtRef')->nullable()
                ->comment("External Ref");
            $table->string('GenPassprt', 1)->nullable();
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
        Schema::dropIfExists('n_n_m1_s');
    }
}
