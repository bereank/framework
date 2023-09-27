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
        Schema::create('data_ownerships', function (Blueprint $table) {
            $table->id();
            $table->string('EmpId');
            $table->integer('ObjType');
            $table->boolean('Active')->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('peer')->default(0);
            $table->integer('manager')->default(0);
            $table->integer('subordinate')->default(0);
            $table->integer('department')->default(0);
            $table->integer('branch')->default(0);
            $table->integer('team')->default(0);
            $table->integer('company')->default(0);
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
        Schema::dropIfExists('data_ownerships');
    }
};
