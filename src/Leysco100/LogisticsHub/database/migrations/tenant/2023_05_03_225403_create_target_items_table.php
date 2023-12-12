<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_items', function (Blueprint $table) {
            $table->id();
            $table->integer('target_setup_id')
                ->references('id')->on('target_setups')->nullable();
            $table->integer('UoM')->nullable();
            $table->string('ItemCode', 50)->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->integer('ItemID')->references('id')->on('o_i_t_m_s')->nullable();
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
        Schema::dropIfExists('target_items');
    }
};
