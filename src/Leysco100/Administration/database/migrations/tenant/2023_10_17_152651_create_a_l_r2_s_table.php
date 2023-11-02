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
        Schema::create('a_l_r2_s', function (Blueprint $table) {
            $table->id();
            $table->integer('Code')->nullable();
            $table->integer('Location')->nullable();
            $table->string('ColName', 30)->nullable();
            $table->boolean('Link')->default(0);
            $table->integer('QueryId')->nullable();
            $table->string('QName', 30)->nullable();
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
        Schema::dropIfExists('a_l_r2_s');
    }
};
