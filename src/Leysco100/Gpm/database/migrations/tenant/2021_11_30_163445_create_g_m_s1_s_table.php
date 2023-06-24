<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGMS1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_m_s1_s', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->default();
            $table->string('DocNum', 100)->nullable();
            $table->string('Location')->nullable();
            $table->string('Longitude', 50)->nullable();
            $table->string('Latitude', 50)->nullable();
            $table->string('AttachPath')->nullable();
            $table->string('Phone')->nullable();
            $table->string('UserSign')->nullable();
            $table->integer('GateID')->nullable();
            $table->integer('DocID')->nullable();
            $table->integer('Status')->default(0)->comment("0=Successfull, 1=Does not Exist, 2=Duplicate,3=Scanned but flagged,4=Rleased");
            $table->integer('Released')->default(0)->comment("0=Not Released, 1=Not Yet");
            $table->string('Comment')->nullable();
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
        Schema::dropIfExists('g_m_s1_s');
    }
}
