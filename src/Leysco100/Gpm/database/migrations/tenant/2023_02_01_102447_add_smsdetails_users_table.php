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
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_username')->nullable()->comment("Api username");
            $table->string('api_key')->nullable()->comment("APi key");
            $table->string('sender_id')->nullable()->comment("sender id");
        });
    }
};
