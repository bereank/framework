<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCQGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_q_g_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('GroupCode', 20)->nullable();
            $table->string('GroupName', 50)->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('Filler', 10)->nullable();
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
        Schema::dropIfExists('o_c_q_g_s');
    }
}
