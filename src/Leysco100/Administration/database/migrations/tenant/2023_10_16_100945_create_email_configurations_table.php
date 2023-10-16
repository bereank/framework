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
        Schema::create('email_configurations', function (Blueprint $table) {
            $table->id();
            $table->Integer('UserSign')->nullable();
            $table->integer('ObjType')->nullable();
            $table->string('MAIL_DRIVER')->nullable();
            $table->string('MAIL_HOST')->nullable();
            $table->string('MAIL_PORT')->nullable();
            $table->string('MAIL_ENCRYPTION')->nullable();
            $table->string('MAIL_USERNAME')->nullable();
            $table->string('MAIL_PASSWORD')->nullable();
            $table->string('MAIL_FROM_NAME')->nullable();
            $table->string('MAIL_FROM_ADDRESS')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configurations');
    }
};
