<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUDG1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_d_g1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('o_u_d_g_id')
                ->references('id')->on('o_u_d_g_s')->nullable();
            $table->integer('user_id')
                ->references('id')->on('users')->nullable();
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
        Schema::dropIfExists('u_d_g1_s');
    }
}
