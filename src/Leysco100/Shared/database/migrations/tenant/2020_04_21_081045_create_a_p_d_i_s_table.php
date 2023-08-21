<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAPDISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_p_d_i_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ObjectID')->unique();
            $table->string('ObjectHeaderTable');
            $table->string('PermissionName')->nullable();
            $table->string('DocumentName')->nullable();
            $table->string('RowStatus', 1)->nullable(); //A--Advanced B--BASIC
            $table->string('JrnStatus', 1)->default("Y"); //Y YES N--NO
            $table->string('isDoc', 1)->default("Y"); //Y YES N--NO
            $table->integer('hasExtApproval')->default(0)->comment("0=Not,1=Yes");
            $table->timestamp('lastUpdated')->default(now());
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
        Schema::dropIfExists('a_p_d_i_s');
    }
}
