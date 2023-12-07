<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOSLTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_s_l_t_s', function (Blueprint $table) {
            $table->id();
            $table->integer('SltCode')->nullable();
            $table->string('ItemCode', 50)->nullable();
            $table->integer('StatusNum')->nullable();
            $table->integer('Owner')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->date('DateCreate')->nullable();
            $table->integer('UpdateBy')->nullable();
            $table->date('DateUpdate')->nullable();
            $table->string('Subject', 200)->nullable();
            $table->string('Symptom', 200)->nullable();
            $table->string('Cause', 200)->nullable();
            $table->string('Descriptio', 16)->nullable();
            $table->string('Attachment', 16)->nullable();
            $table->integer('AtcEntry')->nullable();
            $table->string('Transfered', 1)->nullable();
            $table->integer('Instance')->nullable();
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
        Schema::dropIfExists('o_s_l_t_s');
    }
}
