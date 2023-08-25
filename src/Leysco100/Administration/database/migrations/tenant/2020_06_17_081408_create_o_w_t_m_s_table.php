<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOWTMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_w_t_m_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('WtmCode', 1)->nullable();
            $table->string('Name', 200)->nullable();
            $table->string('Remarks', 200)->nullable();
            $table->string('Conds', 1)->nullable();
            $table->string('Active', 1)->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('PmptChg', 1)->nullable();
            $table->string('AppOnUpd', 1)->nullable();
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
        Schema::dropIfExists('o_w_t_m_s');
    }
}
