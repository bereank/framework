<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOUGPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_u_g_p_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('UgpCode', 100)->uniqu();
            $table->string('UgpName', 100)->nullable();
            $table->integer('BaseUom')
                ->references('id')->on('o_u_o_m_s')->nullable();
            $table->string('DataSource')->nullable();
            $table->string('UserSign')->nullable();
            $table->string('LogInstanc')->nullable();
            $table->string('UserSign2')->nullable();
            $table->string('UpdateDate')->nullable();
            $table->string('CreateDate')->nullable();
            $table->string('IsManual', 1)->default('N');
            $table->string('Locked')->nullable();
            $table->string('ExtRef')->nullable(); //Name
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
        Schema::dropIfExists('o_u_g_p_s');
    }
}
