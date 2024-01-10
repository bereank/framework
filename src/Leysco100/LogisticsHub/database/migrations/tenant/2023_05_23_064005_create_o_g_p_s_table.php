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
        Schema::create('o_g_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('Name')->nullable();
            $table->string('DocNum')->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('OwnerCode')->nullable();
            $table->integer('ObjType')->nullable();
            $table->integer('UpdtFrq')->nullable();
            $table->double('max_latitude')->nullable();
            $table->double('min_latitude')->nullable();
            $table->double('max_longitude')->nullable();
            $table->double('min_longitude')->nullable();
            $table->boolean('Active')->default(true)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('ExtCode')->nullable();
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
        Schema::dropIfExists('o_g_p_s');
    }
};
