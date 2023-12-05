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
        Schema::create('l_s100_modules', function (Blueprint $table) {
            $table->id();
            $table->string('ModuleDescription');
            $table->string('RepoPath');
            $table->integer('installed')->default(0)->comment("0=No, 1=Yes");
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
        Schema::dropIfExists('l_s100_modules');
    }
};
