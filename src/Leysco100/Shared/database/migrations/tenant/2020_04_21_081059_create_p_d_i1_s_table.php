<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePDI1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_d_i1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('DocEntry')
                ->references('id')->on('a_p_d_i_s');
            $table->string('ChildTable');
            $table->string('ChildType')->nullable();
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
        Schema::dropIfExists('p_d_i1_s');
    }
}
