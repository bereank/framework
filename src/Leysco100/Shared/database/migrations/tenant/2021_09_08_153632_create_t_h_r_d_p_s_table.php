<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTHRDPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_h_r_d_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('TransID', 20)->nullable();
            $table->string('ActCode')->nullable()->comment("Accounts Details");
            $table->string('CntName')->nullable();
            $table->string('CntPhone')->nullable();
            $table->string('TransAmount', 20)->nullable();
            $table->string('TransTime')->nullable();
            $table->float('Balance')->nullable();
            $table->string('ExtRef')->nullable();
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
        Schema::dropIfExists('t_h_r_d_p_s');
    }
}
