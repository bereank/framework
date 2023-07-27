<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOTERSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_t_e_r_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descript', 200)->nullable(); //Name
            $table->integer('parent')
                ->references('id')->on('o_t_e_r_s')->nullable();
            $table->integer('index')->nullable(); //Location Index
            $table->string('inactive', 1)->default('N');
            $table->string('ExtRef')->nullable(); //Name
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
        Schema::dropIfExists('o_t_e_r_s');
    }
}
