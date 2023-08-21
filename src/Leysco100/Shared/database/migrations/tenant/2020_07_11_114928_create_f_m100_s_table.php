<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFM100STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_m100_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->string('Label');
            $table->string('ParentID')->nullable();
            $table->string('Visible');
            $table->string('icon')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('f_m100_s');
    }
}
