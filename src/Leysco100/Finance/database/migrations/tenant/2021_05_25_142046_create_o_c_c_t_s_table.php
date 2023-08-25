<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCCTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_c_t_s', function (Blueprint $table) {
            $table->id();
            $table->string('CctCode');
            $table->string('CctName');
            $table->string('ExtRef')->nullable()->comment("External Ref");
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
        Schema::dropIfExists('o_c_c_t_s');
    }
}
