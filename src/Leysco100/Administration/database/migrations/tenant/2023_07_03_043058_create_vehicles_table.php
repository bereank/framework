<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('RegistrationNO')->unique();
            $table->string('Make')->nullable();
            $table->string('Model')->nullable();
            $table->string('Brand')->nullable();
            $table->integer('Year')->nullable();
            $table->string('Color')->nullable();
            $table->integer('Capacity')->nullable()->default(0);
            $table->boolean('Status')->default(true)->comment('1 => active , 2 => inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
