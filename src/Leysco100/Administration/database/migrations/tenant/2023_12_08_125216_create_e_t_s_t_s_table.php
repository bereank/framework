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
        Schema::create('e_t_s_t_s', function (Blueprint $table) {
            $table->id();
            $table->integer('ObjType')->nullable();
            $table->boolean('Active')->nullable();
            $table->string('Code')->nullable();
            $table->string('Name')->nullable();
          
            $table->time('CheckInTime')->nullable();
            $table->time('CheckOutTime')->nullable();
            $table->integer('LogType')->nullable()->default(0);
            $table->integer('OwnerCode')->nullable();
            $table->integer('UserSign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_t_s_t_s');
    }
};
