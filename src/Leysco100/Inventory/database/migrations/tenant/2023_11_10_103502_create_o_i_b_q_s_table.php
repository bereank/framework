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
        Schema::create('o_i_b_q_s', function (Blueprint $table) {
            $table->id();
            $table->integer('AbsEntry')->nullable();
            $table->string('ItemCode', 20)->nullable();
            $table->integer('BinAbs')->nullable();
            $table->decimal('OnHandQty', 19, 6)->nullable();
            $table->string('WhsCode',50)->nullable();
            $table->boolean('Freezed')->default(0);
            $table->integer('FreezeDoc')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_i_b_q_s');
    }
};
