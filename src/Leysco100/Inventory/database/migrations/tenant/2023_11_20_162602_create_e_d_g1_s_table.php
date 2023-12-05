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
        Schema::create('e_d_g1_s', function (Blueprint $table) {
            $table->id();
            $table->integer('DocEntry')->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->string('ObjKey', 50)->nullable();
            $table->char('DiscType', 1)->nullable();
            $table->floa('Discount', 19, 6)->default(0);
            $table->floa('PayFor', 19, 6)->default(0);
            $table->floa('ForFree', 19, 6)->default(0);
            $table->floa('UpTo', 19, 6)->default(0);
            $table->integer('LogInstanc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_d_g1_s');
    }
};
