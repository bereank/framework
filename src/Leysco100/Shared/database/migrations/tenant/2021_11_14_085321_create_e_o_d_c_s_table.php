<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEODCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_o_d_c_s', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType');
            $table->string('ExtFieldName')->nullable()->comment("External Field Used to Query the data");
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
            $table->string('DocNum', 100)->nullable()->comment("Leysco DocNum");
            $table->integer('DocEntry')->nullable()->comment("Leysco Doc Entry");
            $table->integer('UserSign')->nullable();
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
        Schema::dropIfExists('e_o_d_c_s');
    }
}
