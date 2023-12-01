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
            $table->string('ItemCode');
            $table->string('CardCode');
            $table->integer('LINENUM');
            $table->string('Price', 10);
            $table->string('Currency');
            $table->string('Discount', 10);
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
