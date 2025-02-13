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
        Schema::create('o_u_t_b', function (Blueprint $table) {
            $table->id();
            $table->integer('ObjectType');
            $table->string('TableName')->nullable();
            $table->string('Descr')->nullable();
            $table->string('TableID')->nullable();
            $table->string('UserValue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_u_t_b');
    }
};
