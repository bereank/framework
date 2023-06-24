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
        Schema::create('mobile_nav_bars', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('key', 50);
            $table->string('status')->default(0)->comment("1 active, 0 in-active");
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
        Schema::dropIfExists('mobile_nav_bars');
    }
};
