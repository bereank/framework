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
            $table->integer('FormID', 20)->nullable();
            $table->string('ItemID', 50)->nullable();
            $table->integer('ColID', 20)->nullable();
            $table->integer('ActionT', 6)->nullable();
            $table->integer('QueryId', 6)
                ->references('id')->on('o_u_q_r')->nullable();
            $table->integer('IndexID', 6)->nullable();
            $table->integer('Refresh', 1)->nullable();
            $table->string('FieldID', 60)->nullable();
            $table->integer('FrceRfrsh', 1)->nullable();
            $table->integer('ByField', 1)->nullable();
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
