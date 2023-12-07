<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSER1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_e_r1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('MenuID')
                ->references('id')->on('f_m100_s')->nullable();
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
        Schema::dropIfExists('s_e_r1_s');
    }
}
