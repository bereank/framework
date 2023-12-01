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
        Schema::create('o_s_p_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode')->nullable();
            $table->string('CardCode')->nullable();
            $table->string('Price')->nullable();
            $table->string('Currency', 3)->nullable();
            $table->string('Discount')->nullable();
            $table->integer('ListNum')->nullable();
            $table->char('AutoUpdt', 1)->nullable();
            $table->char('EXPAND', 1)->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('SrcPrice')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->char('Valid', 1)->nullable();
            $table->date('ValidFrom')->nullable();
            $table->date('ValidTo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_s_p_p_s');
    }
};
