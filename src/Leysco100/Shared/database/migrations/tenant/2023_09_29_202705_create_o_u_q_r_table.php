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
        Schema::create('o_u_q_r', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->nullable();
            $table->string('UserSign')->nullable();
            $table->integer('QCategory')->nullable();
            $table->string('QName');
            $table->longText('QString')->nullable();
            $table->char('QType')->nullable()->comment('G= Report Generator, R=Regular, S=Stored Procedure, W=Wizard');
            $table->integer('DBType')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_u_q_r');
    }
};
