<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSCL4STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_c_l4_s', function (Blueprint $table) {
            $table->id();
            $table->integer('SrcvCallID')->nullable();
            $table->integer('Line')->nullable();
            $table->string('PartType', 1)->nullable();
            $table->integer('DocAbs')->nullable();
            $table->string('Object', 20)->nullable();
            $table->date('DocPstDate', 1)->nullable();
            $table->integer('ObjectType')->nullable();
            $table->integer('LogInstanc')->nullable();
            $table->integer('UserSign')->nullable();
            $table->date('CreateDate')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->integer('DocNumber')->nullable();
            $table->string('Transfered', 1)->nullable();
            $table->string('VisOrder', 1)->nullable();
            $table->string('StckTrnDir', 1)->nullable();
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
        Schema::dropIfExists('s_c_l4_s');
    }
}
