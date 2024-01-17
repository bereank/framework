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
        Schema::create('o_r_a_s', function (Blueprint $table) {
            $table->id();
            $table->dateTime("Date");
            $table->string('Name')->nullable();
            $table->string('Code')->nullable();
            $table->string('Description')->nullable();
            $table->string("Repeat")->nullable();
            $table->integer("RouteID")
                ->references("id")->on("o_r_p_s");
            $table->integer("SlpCode")
                ->references("SlpCode")->on("o_s_l_p_s");
            $table->string('DocNum')->nullable();
            $table->integer('UserSign')->nullable()->references('id')->on('users');
            $table->integer('OwnerCode')->nullable();
            $table->integer('ObjType')->nullable();
            $table->boolean('CreateCall')->default(0)->nullable();
            $table->boolean('Active')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_r_a_s');
    }
};
