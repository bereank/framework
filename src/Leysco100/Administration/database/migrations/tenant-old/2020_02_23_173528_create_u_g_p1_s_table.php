<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUGP1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_g_p1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('UgpEntry')
                ->references('id')->on('o_u_g_p_s')->nullable();
            $table->integer('UomEntry')
                ->references('id')->on('o_u_o_m_s')->nullable();
            $table->float('AltQty', 19, 6)->nullable();
            $table->float('BaseQty', 19, 6)->nullable();
            $table->string('LogInstanc')->nullable();
            $table->string('LineNum')->nullable();
            $table->string('WghtFactor')->nullable();
            $table->string('UdfFactor')->nullable();
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
        Schema::dropIfExists('u_g_p1_s');
    }
}
