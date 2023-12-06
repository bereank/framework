<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSRI1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_r_i1_s', function (Blueprint $table) {
            $table->id();
            $table->string('ItemCode', 20)->nullable();
            $table->integer('SysSerial')->nullable();
            $table->integer('LineNum')->nullable();
            $table->integer('BaseType')->nullable();
            $table->integer('BaseEntry')->nullable();
            $table->integer('BaseNum')->nullable();
            $table->integer('BaseLinNum')->nullable();
            $table->date('DocDate')->nullable();
            $table->string('WhsCode', 8)->nullable();
            $table->string('CardCode', 15)->nullable();
            $table->string('CardName', 100)->nullable();
            $table->string('Direction', 1)->nullable();
            $table->date('CreateDate', 1)->nullable();
            $table->string('ItemName', 100)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('UserSign')->nullable();
            $table->integer('BsDocType')->nullable();
            $table->integer('BsDocEntry')->nullable();
            $table->integer('BsDocLine')->nullable();
            $table->string('UpgCurNode', 1)->nullable();
            $table->string('UpgParent', 1)->nullable();
            $table->string('UpgSortId', 1)->nullable();
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
        Schema::dropIfExists('s_r_i1_s');
    }
}
