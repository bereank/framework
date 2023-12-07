<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSCL2STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_c_l2_s', function (Blueprint $table) {
            $table->id();
            $table->integer('SrcvCallID')->nullable();
            $table->integer('Line')->nullable();
            $table->string('ItemCode', 50)->nullable();
            $table->string('ItemName', 100)->nullable();
            $table->float('TransToTec', 19, 3)->default(0);
            $table->float('Delivered', 19, 3)->default(0);
            $table->float('RetFromTec', 19, 3)->default(0);
            $table->float('Returned', 19, 3)->default(0);
            $table->string('Bill', 1)->default("Y");
            $table->float('QtyToBill', 19, 3)->default(0);
            $table->float('QtyToInv', 19, 3)->default(0);
            $table->string('ObjectType', 20)->nullable();
            $table->integer('LogInstanc')->nullable();
            $table->integer('UserSign')->nullable();
            $table->date('CreateDate')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->integer('VisOrder')->nullable();
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
        Schema::dropIfExists('s_c_l2_s');
    }
}
