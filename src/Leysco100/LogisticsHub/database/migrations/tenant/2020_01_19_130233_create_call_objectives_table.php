<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_objectives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CallCode')
                ->references('id')->on('o_c_l_g_s');
            $table->string('Objective');
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
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
        Schema::dropIfExists('call_objectives');
    }
}
