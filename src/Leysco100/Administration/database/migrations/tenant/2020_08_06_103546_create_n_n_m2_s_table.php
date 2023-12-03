<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNNM2STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('n_n_m2_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ObjectCode')->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('Series')->nullable();
            $table->string('ExtRef')->nullable()
                ->comment("External Ref");
            $table->string('DocSubType', 2)->nullable();
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
        Schema::dropIfExists('n_n_m2_s');
    }
}
