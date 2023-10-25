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
        Schema::create('a_l_t2', function (Blueprint $table) {
            $table->id();
            $table->integer('DocEntry')->nullable();
            $table->string('Code')->nullable();      
            $table->unsignedBigInteger('GroupId')->nullable();
            $table->string('UserSign')->nullable();
            $table->boolean('SendIntrnl')->default(false);
            $table->boolean('SendEMail')->default(false);
            $table->boolean('SendSMS')->default(false);
            $table->boolean('SendFax')->default(false);
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
        Schema::dropIfExists('a_l_t2');
    }
};
