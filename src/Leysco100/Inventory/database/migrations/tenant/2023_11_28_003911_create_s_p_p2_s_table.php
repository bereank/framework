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
        Schema::create('s_p_p2_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->string('CardCode')->nullable();
            $table->string('SPP1LNum')->nullable();
            $table->string('SPP2LNum')->nullable();
            $table->float('Amount', 19, 6)->nullable();
            $table->float('Price', 19, 6)->nullable();
            $table->string('Currency', 3)->nullable();
            $table->float('Discount', 19, 6)->nullable();
            $table->integer('DiscType')->default(1);
            $table->string('UomEntry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_p_p2_s');
    }
};
