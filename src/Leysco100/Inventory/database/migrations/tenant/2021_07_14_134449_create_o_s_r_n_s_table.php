<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOSRNSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_s_r_n_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->integer('SysNumber')->nullable();
            $table->string('DistNumber', 36)->nullable();
            $table->string('MnfSerial', 36)->nullable();
            $table->string('LotNumber', 36)->nullable();
            $table->date('ExpDate')->nullable();
            $table->date('MnfDate')->nullable();
            $table->date('InDate')->nullable();
            $table->date('GrntStart', 1)->nullable();
            $table->date('GrntExp', 1)->nullable();
            $table->date('CreateDate')->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('Status', 1)->nullable();
            $table->string('Notes', 16)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('Transfered', 1)->nullable();
            $table->string('Instance', 1)->nullable();
            $table->string('AbsEntry', 1)->nullable();
            $table->string('ObjType')->nullable();
            $table->string('ItemName', 100)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('CostTotal', 1)->nullable();
            $table->float('Quantity', 19, 2)->nullable();
            $table->float('QuantOut', 19, 2)->nullable();
            $table->float('PriceDiff', 19, 2)->nullable();
            $table->float('Balance', 19, 2)->nullable();
            $table->string('TrackingNt', 1)->nullable();
            $table->string('TrackiNtLn', 1)->nullable();
            $table->string('SumDec', 1)->nullable();
            $table->string('Colour')->nullable();
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
        Schema::dropIfExists('o_s_r_n_s');
    }
}
