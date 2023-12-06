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
        Schema::create('s_p_p1_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->string('CardCode')->nullable();
            $table->integer('LINENUM')->nullable();
            $table->float('Price', 19, 6)->nullable();
            $table->string('Currency')->nullable();
            $table->float('Discount', 19, 6)->nullable();
            $table->integer('ListNum');
            $table->date('FromDate');
            $table->date('ToDate');
            $table->boolean('AutoUpdt');
            $table->string('Expand');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_p_p1_s');
    }
};
