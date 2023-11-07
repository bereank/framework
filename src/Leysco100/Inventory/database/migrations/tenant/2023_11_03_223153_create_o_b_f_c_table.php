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
        Schema::create('o_b_f_c', function (Blueprint $table) {
            $table->id();
            $table->char('FldType', 1)->nullable();
            $table->integer('FldNum')->nullable();
            $table->string('DispName', 50)->nullable();
            $table->string('KeyName', 50)->nullable();
            $table->boolean('Activated')->default(0);
            $table->integer('UserSign')->nullable();
            $table->char('DataSource', 1)->nullable();
            $table->char('Transfered', 1)->nullable();
            $table->integer('Instance')->nullable();
            $table->integer('LogInstanc')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->string('DftName', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_f_c');
    }
};
