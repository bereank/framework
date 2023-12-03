<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateODIMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_d_i_m_s', function (Blueprint $table) {
            $table->id();
            $table->integer('DimCode')->nullable();
            $table->string('DimName', 15)->nullable();
            $table->string('DimActive', 1)->default("N")->comment("N=No,Y=Yes");
            $table->string('DimDesc', 50)->nullable();
            $table->string('ExtRef')->nullable()->comment("External Ref");
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
        Schema::dropIfExists('o_d_i_m_s');
    }
}
