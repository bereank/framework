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
        Schema::create('a_l_r3_s', function (Blueprint $table) {
            $table->id();
            $table->integer('Code')->nullable();
            $table->integer('Location')->nullable();
            $table->integer('Line')->nullable();
            $table->string('Value', 254)->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->string('KeyStr', 254)->nullable();
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
        Schema::dropIfExists('a_l_r3_s');
    }
};
