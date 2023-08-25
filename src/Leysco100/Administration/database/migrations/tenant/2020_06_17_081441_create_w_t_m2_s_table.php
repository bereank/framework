<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWTM2STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_t_m2_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('WtmCode')->nullable(); //Approval Templates
            $table->integer('WstCode')->nullable(); //Approval Stages
            $table->integer('SortId')->nullable();
            $table->string('Remarks', 200)->nullable();
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
        Schema::dropIfExists('w_t_m2_s');
    }
}
