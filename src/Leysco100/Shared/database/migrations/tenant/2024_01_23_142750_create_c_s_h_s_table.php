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
        Schema::create('c_s_h_s', function (Blueprint $table) {
            $table->id();
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->integer('FormID')->nullable();
            $table->string('ItemID')->nullable();
            $table->integer('ColID')->nullable();
            $table->integer('ActionT')->nullable();
            $table->integer('QueryId')
                ->references('id')->on('o_u_q_r')->nullable();
            $table->integer('IndexID')->nullable();
            $table->integer('Refresh')->nullable();
            $table->string('FieldID')->nullable();
            $table->integer('FrceRfrsh')->nullable();
            $table->integer('ByField')->nullable();
            $table->string('ObjType')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_s_h_s');
    }
};
