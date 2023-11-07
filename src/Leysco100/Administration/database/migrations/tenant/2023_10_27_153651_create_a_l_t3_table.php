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
        Schema::create('a_l_t3', function (Blueprint $table) {
            $table->id();
            $table->integer('DocEntry')->nullable();
            $table->integer('Code')->nullable();
            $table->string("variable_key")->unique();
            $table->text("variable_value")->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->string('UserSign')->nullable();
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
        Schema::dropIfExists('a_l_t3');
    }
};
