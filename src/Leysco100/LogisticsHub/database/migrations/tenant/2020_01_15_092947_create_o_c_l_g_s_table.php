<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCLGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_l_g_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ClgCode', 1)->nullable();
            $table->integer('CardCode')
                ->references('id')->on('o_c_d_s');
            $table->integer('SlpCode')->nullable();
            $table->integer('RouteCode')->nullable();
            $table->date('CallDate')->nullable();
            $table->time('CallTime')->nullable();
            $table->time('CallEndTime')->nullable();
            $table->date('CloseDate')->nullable();
            $table->time('CloseTime')->nullable();
            $table->date('OpenedDate')->nullable();
            $table->date('OpenedTime')->nullable();
            $table->string('Repeat')->default('N');
            $table->string('Summary')->nullable();
            $table->string('Status', 1)->default('D');//O Opened //D Due,C Closed, A  Abondened
            $table->string('AprovalStatus', 1)->default('P'); // A,Approved,P Pending
            $table->integer('UserSign');
            $table->integer('CountryID')->references('id')->on('countries')->nullable();
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
        Schema::dropIfExists('o_c_l_g_s');
    }
}
