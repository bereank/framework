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
        Schema::create('t_p_p2_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('DocEntry');
            $table->string('Code')->nullable();
            $table->boolean('MobileActive')->default(0)->nullable();;
            $table->integer('Status')->nullable();
            $table->string('Shortcode')->nullable();
            $table->string('Username')->nullable();
            $table->string('Password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_p_p2_s');
    }
};
