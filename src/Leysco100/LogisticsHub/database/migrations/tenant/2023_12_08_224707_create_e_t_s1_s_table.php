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
        Schema::create('e_t_s1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('UserSign')->nullable();
            $table->integer('DocEntry')->nullable();
            $table->date('Date')->nullable();
            $table->string('Status')->nullable();
            $table->time('ClockIn')->nullable();
            $table->time('ClockOut')->nullable();
            $table->time('Late')->nullable();
            $table->time('EarlyLeaving')->nullable();
            $table->time('OverTime')->nullable();
            $table->time('TotalRest')->nullable();
            $table->string('Comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_t_s1_s');
    }
};
