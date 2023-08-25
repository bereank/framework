<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOPRCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_p_r_c_s', function (Blueprint $table) {
            $table->id();
            $table->string('PrcCode', 8)->nullable();
            $table->string('PrcName', 30)->nullable();
            $table->string('GrpCode', 4)->nullable();
            $table->float('Balance', 19, 6)->default(0);
            $table->string('Locked', 1)->default("N");
            $table->string('UserSign', 1)->nullable();
            $table->integer('DimCode')->nullable()->comment("Refrences Dimensions");
            $table->integer('CCTypeCode')->nullable();
            $table->date('ValidFrom')->nullable();
            $table->date('ValidTo')->nullable();
            $table->string('Active', 1)->default("Y");
            $table->string('UserSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('CCOwner', 1)->nullable();
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
        Schema::dropIfExists('o_p_r_c_s');
    }
}
