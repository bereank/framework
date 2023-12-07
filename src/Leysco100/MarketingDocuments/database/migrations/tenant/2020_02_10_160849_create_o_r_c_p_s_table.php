<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateORCPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_r_c_p_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AbsEntry', 1)->nullable();
            $table->string('Code', 8)->nullable();
            $table->string('Dscription', 50)->nullable();
            $table->string('IsRemoved', 1)->default('N');
            $table->string('DocObjType', 20)->default(-1);
            $table->string('DraftEntry')
                ->references('id')->on('o_d_r_f_s')->nullable();
            $table->string('Frequency', 1)->default('M');
            $table->string('Remind')->default(1001);
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('CreateUser')
                ->references('id')->on('users')->nullable();
            $table->string('UpdateUser')
                ->references('id')->on('users')->nullable();
            $table->string('DataSource', 1)->default('N');
            $table->string('PriceUpdat', 1)->default('N');
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
        Schema::dropIfExists('o_r_c_p_s');
    }
}
