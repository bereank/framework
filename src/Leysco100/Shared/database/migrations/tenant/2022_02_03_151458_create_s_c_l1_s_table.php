<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSCL1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_c_l1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('srvcCallID')->nulable();
            $table->integer('line')->nulable();
            $table->integer('solutionID')->nulable();
            $table->string('objType', 20)->nulable();
            $table->integer('logInstanc')->nulable();
            $table->integer('userSign')->nulable();
            $table->date('createDate')->nulable();
            $table->integer('userSign2')->nulable();
            $table->integer('updateDate')->nulable();
            $table->integer('VisOredr')->nulable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
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
        Schema::dropIfExists('s_c_l1_s');
    }
}
