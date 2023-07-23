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
        Schema::create('back_up_mod_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('UserSign')->nullable();
            $table->foreignId('BackupModeID')->nullable();
            $table->integer('GateID')->nullable();
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
        Schema::dropIfExists('back_up_mod_users');
    }
};
