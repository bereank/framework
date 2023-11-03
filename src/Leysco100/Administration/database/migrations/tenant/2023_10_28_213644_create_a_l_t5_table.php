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
        Schema::create('a_l_t5', function (Blueprint $table) {
            $table->id();
            $table->integer('DocEntry')->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('tempSubject')->nullable();
            $table->string('tempTitle')->nullable();
            $table->string('tempCode')->nullable();
            $table->string('tempBody')->nullable();
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
        Schema::dropIfExists('a_l_t5');
    }
};
