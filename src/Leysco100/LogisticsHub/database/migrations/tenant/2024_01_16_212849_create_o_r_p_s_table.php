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
        Schema::create('o_r_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('Code')->nullable();
            $table->string('Description')->nullable();
            $table->double('StartLng')->nullable();
            $table->double('StartLat')->nullable();
            $table->double('EndLat')->nullable();
            $table->double('EndLng')->nullable();
            $table->string('StartLocName')->nullable();
            $table->string('EndLocName')->nullable();
            $table->string('DocNum')->nullable();
            $table->integer('UserSign')->nullable()->references('id')->on('users');
            $table->integer('OwnerCode')->nullable();
            $table->integer('ObjType')->nullable();
            $table->string('ExtCode')->nullable();
            $table->boolean('Active')->default(0)->nullable();
            $table->integer('TerritoryID')->nullable();
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
        Schema::dropIfExists('o_r_p_s');
    }
};
