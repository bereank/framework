<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCR1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_r1_s', function (Blueprint $table) {
            $table->id();
            $table->string('OcrCode', 8)->nullable();
            $table->integer('PrcCode')->nullable()->comment("Refrence Cost Centers");
            $table->float('PrcAmount', 19, 6)->default(0);
            $table->float('OcrTotal', 19, 6)->default(0);
            $table->string('Direct', 1)->default("N");
            $table->string('UserSign', 1)->nullable();
            $table->date('ValidFrom')->nullable();
            $table->date('ValidTo', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->date('updateDate')->nullable();
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
        Schema::dropIfExists('o_c_r1_s');
    }
}
