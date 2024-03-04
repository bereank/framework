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
        Schema::create('t_p_p1_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('DocEntry');
            $table->string('Name')->nullable();
            $table->string('Code')->nullable();
            $table->integer('AuthMth')->nullable();
            $table->boolean('HasStkPush')->nullable();
            $table->string('CallBackUrl')->nullable();
            $table->string('ValidationUrl')->nullable();
            $table->string('ConfirmUrl')->nullable();
            $table->string('StkPushURL')->nullable();
            $table->string('QueryUrl')->nullable();
            $table->boolean('Active')->default(0)->nullable();
            $table->string('PassKey')->nullable();
            $table->string('PublicKeyPath')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_p_p1_s');
    }
};
