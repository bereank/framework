<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('UoM')->nullable();
            $table->float('Avalue', 19, 6)->nullable(); //Target Achieved
            $table->float('Tvalue', 19, 6)->nullable(); //Target Value
            $table->string('TargetType', 1)->nullable(); //O--OUOM, M--Monetory
            $table->integer('target_setup_id')
                ->references('id')->on('target_setups')->nullable();
            $table->date('PeriodStart');
            $table->date('PeriodEnd');
            $table->string('TCode')->nullable();
            $table->string('TName')->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
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
        Schema::dropIfExists('targets');
    }
};
