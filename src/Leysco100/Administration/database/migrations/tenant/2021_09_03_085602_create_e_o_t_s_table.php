<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEOTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_o_t_s', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType');
            $table->integer('DocEntry');
            $table->text('ErrorMessage')->nullable();
            $table->integer('status')->comments("0=Unread, 1=read");
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
        Schema::dropIfExists('e_o_t_s');
    }
}
