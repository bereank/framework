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
        Schema::create('o_u_g_r', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('GroupId')->nullable();
            $table->string('GroupName', 155)->nullable();
            $table->string('GroupDec', 155)->nullable();
            $table->text('Allowences');
            $table->unsignedBigInteger('TPLId')->nullable();
            $table->date('StartDate');
            $table->date('DueDate');
            $table->char('GroupType', 1)->nullable();
            $table->unsignedBigInteger('CockpitId')->nullable();
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('o_u_g_r');
    }
};
