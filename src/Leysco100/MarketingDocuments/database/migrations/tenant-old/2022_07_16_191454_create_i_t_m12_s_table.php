<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITM12STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_t_m12_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode', 50)->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->string('UomType', 1)->default('S');
            $table->integer('UomEntry')->nullable();
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
        Schema::dropIfExists('i_t_m12_s');
    }
}
