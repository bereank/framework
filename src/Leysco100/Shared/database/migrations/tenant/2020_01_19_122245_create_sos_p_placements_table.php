<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSosPPlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sos_p_placements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ItemGrpCode')
                ->references('id')->on('o_i_t_b_s');
            $table->integer('CallCode')
                ->references('id')->on('o_c_l_g_s');
            $table->integer('CardCode')
                ->references('id')->on('o_c_d_s');
            $table->float('AllotedShelfSize', 19, 2)->nullable();
            $table->float('ShelfSize', 19, 2)->nullable();
            $table->string('PcmtBlocked')->default('N');
            $table->string('PcmtEyeLevel')->default('N');
            $table->string('PcmtFocusArea')->default('N');
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
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
        Schema::dropIfExists('sos_p_placements');
    }
}
