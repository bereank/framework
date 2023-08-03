<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOSLPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_s_l_p_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('SlpCode')->nullable();
            $table->string('SlpName', 150);
            $table->string('Memo', 50)->nullable();
            $table->integer('Commission')->nullable();
            $table->string('GroupCode', 1)->nullable();
            $table->string('Locked', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->integer('EmpID')->nullable();
            $table->string('Active', 1)->nullable(); ////N=Inactive, Y=Active
            $table->string('Telephone', 50)->nullable();
            $table->string('Mobil', 50)->nullable();
            $table->string('Fax', 1)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->integer('ChannCode')
                ->references('id')->on('channels')->nullable();
            $table->integer('TierCode')
                ->references('id')->on('tiers')->nullable();
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
        Schema::dropIfExists('o_s_l_p_s');
    }
}
