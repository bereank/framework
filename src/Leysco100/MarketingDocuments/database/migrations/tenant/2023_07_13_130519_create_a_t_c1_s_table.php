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
        Schema::create('a_t_c1_s', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("AbsEntry")
                ->references('id')->on('o_a_t_c_s');
            $table->integer("Line")->autoIncrement(0);
            $table->text("srcPath")->nullable();
            $table->text("trgtPath");
            $table->string("FileName");
            $table->string("FileExt");
            $table->date("Date");
            $table->integer("UsrID");
            $table->string("Copied")->nullable();
            $table->string("Override")->nullable();
            $table->string("subPath")->nullable();
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
        Schema::dropIfExists('a_t_c1_s');
    }
};
