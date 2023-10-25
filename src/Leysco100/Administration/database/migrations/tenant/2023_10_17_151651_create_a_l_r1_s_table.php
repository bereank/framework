<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{  /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_l_r1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('Code')->nullable();
            $table->integer('LineId')->nullable();
            $table->string('NameFrom', 155)->nullable();
            $table->string('AddrFrom', 100)->nullable();
            $table->string('NameTo', 155)->nullable();
            $table->char('IsSMS', 1)->nullable();
            $table->string('Address', 100)->nullable();
            $table->boolean('Status')->default(1);
            $table->string('ObjType', 20)->nullable();
            $table->string('ObjCode', 50)->nullable();
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
        Schema::dropIfExists('a_l_r1_s');
    }
};
