<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOSRQSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_s_r_q_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->integer('SysNumber')->nullable();
            $table->string('WhsCode')->nullable();
            $table->float('Quantity', 19, 2)->default(0);
            $table->float('CommitQty', 19, 2)->default(0);
            $table->float('CountQty', 19, 2)->default(0);
            $table->integer('AbsEntry')->nullable();
            $table->integer('MdAbsEntry')->nullable();
            $table->integer('TrackingNt')->nullable();
            $table->integer('TrackiNtLn')->nullable();
            $table->float('CCDQuant', 19, 6)->default(0);
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
        Schema::dropIfExists('o_s_r_q_s');
    }
}
