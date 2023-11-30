<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('s_p_p3_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->string('CardCode')->nullable();
            $table->string('SPP2Num')->nullable();
            $table->string('MaxForFree')->nullable();
            $table->string('Quantity')->nullable();
            $table->string('Price')->default(0);
            $table->string('Currency', 3)->nullable();
            $table->string('UomEntry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_p_p3_s');
    }
};
