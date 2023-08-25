<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWTM3STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_t_m3_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('WtmCode', 1)->nullable(); //Approval Templates
            $table->string('TransType')->nullable(); //Original Document
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
        Schema::dropIfExists('w_t_m3_s');
    }
}
