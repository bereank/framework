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
        Schema::create('o_e_d_g_s', function (Blueprint $table) {
            $table->id();
            $table->char('Type', 1)->nullable();
            $table->string('ObjType', 50)->nullable();
            $table->string('ObjCode', 50)->nullable();
            $table->string('DiscRel', 10)->nullable();
            $table->string('ValidFor', 50)->nullable();
            $table->date('ValidForm')->nullable();
            $table->date('ValidTo')->nullable();
            $table->char('DataSource', 1)->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_e_d_g_s');
    }
};
