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
        Schema::create('c_r_d16_s', function (Blueprint $table) {
            $table->id();
            $table->integer('RouteID')->nullable();
            $table->boolean('IsDefault')->default(0)->nullable();
            $table->integer('CardCode')->nullable();
            $table->integer('CardName')->nullable();
            $table->integer('UserSign')->nullable()->references('id')->on('users');
            $table->integer('OwnerCode')->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_r_d16_s');
    }
};
