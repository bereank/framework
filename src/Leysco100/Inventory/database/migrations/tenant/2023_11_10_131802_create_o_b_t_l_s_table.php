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
        Schema::create('o_b_t_l_s', function (Blueprint $table) {
            $table->id();
            $table->decimal('AbsEntry', 6)->nullable();
            $table->decimal('MessageID', 6)->nullable();
            $table->decimal('BinAbs', 6)->nullable();
            $table->decimal('SnBMDAbs', 6)->nullable();
            $table->decimal('Quantity', 19, 6)->nullable();
            $table->decimal('ITLEntry', 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_t_l_s');
    }
};
