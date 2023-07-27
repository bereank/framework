<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOUDPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_u_d_p_s', function (Blueprint $table) {
            $table->id();
            $table->integer('Code')->nullable();
            $table->string('Name', 20)->nullable();
            $table->string('Remarks', 100)->nullable();
            $table->integer('Father')->references('id')->on('o_u_d_p_s')->nullable();
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
        Schema::dropIfExists('o_u_d_p_s');
    }
}
