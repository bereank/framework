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
        Schema::create('target_sales_emps', function (Blueprint $table) {
            $table->id();
            $table->integer('target_setup_id')
                ->references('id')->on('target_setups')->nullable();
            $table->integer('SlpCode')->references('SlpCode')->on('o_s_l_p_s')->nullable();
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
        Schema::dropIfExists('target_sales_emps');
    }
};
