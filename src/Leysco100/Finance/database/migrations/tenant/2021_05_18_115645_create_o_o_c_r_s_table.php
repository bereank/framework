<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOOCRSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_o_c_r_s', function (Blueprint $table) {
            $table->id();
            $table->string('OcrCode', 8)->nullable();
            $table->string('OcrName', 30)->nullable();
            $table->float('OcrTotal', 19, 6)->default(0);
            $table->string('Direct', 1)->default("N");
            $table->string('Locked', 1)->default("N");
            $table->string('UserSign', 1)->nullable();
            $table->integer('DimCode')->nullable()->comment("Refrences Dimensions");
            $table->string('AbsEntry', 1)->nullable();
            $table->string('Active', 11)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('IsFixedAmt', 1)->nullable();
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
        Schema::dropIfExists('o_o_c_r_s');
    }
}
