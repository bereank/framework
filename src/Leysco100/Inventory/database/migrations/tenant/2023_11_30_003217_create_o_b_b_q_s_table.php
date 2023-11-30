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
        Schema::create('o_b_b_q_s', function (Blueprint $table) {
            $table->id();
            $table->Integer('AbsEntry')->nullable();
            $table->string('ItemCode', 150)->nullable();
            $table->Integer('SnBMDAbs')->nullable();
            $table->Integer('BinAbs')->nullable();
            $table->decimal('OnHandQty', 19, 6)->nullable();
            $table->string('WhsCode', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_b_q_s');
    }
};
