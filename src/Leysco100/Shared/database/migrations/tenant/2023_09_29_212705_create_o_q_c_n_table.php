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
        Schema::create('o_q_c_n', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('CategoryId')->nullable();
            $table->string('PermMask')->nullable();
            $table->longText('CatName');
            $table->char('DataSource')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_q_c_n');
    }
};
