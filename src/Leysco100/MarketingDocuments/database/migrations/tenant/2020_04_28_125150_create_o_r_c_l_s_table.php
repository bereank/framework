<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateORCLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_r_c_l_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AbsEntry', 1)->nullable();
            $table->integer('RcpEntry')
                ->references('id')->on('o_r_c_p_s')->nullable();
            $table->integer('Instance')->default(0);
            $table->date('PlanDate', 1)->nullable();
            $table->string('Status', 1)->default('N'); //E=Executed, N=Not Executed, R=Removed
            $table->string('DocObjType', 30)->nullable(); //	112=Draft, 13=Invoice
            $table->string('DocEntry', 1)->nullable();
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
        Schema::dropIfExists('o_r_c_l_s');
    }
}
