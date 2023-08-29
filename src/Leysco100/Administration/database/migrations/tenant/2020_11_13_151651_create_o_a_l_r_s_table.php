<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOALRSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_a_l_r_s', function (Blueprint $table) {
            $table->id();
            $table->string('Code', 1)->nullable();
            $table->string('Type', 1)->default('A');
            $table->string('Priority', 1)->nullable();
            $table->string('TCode', 1)->nullable();
            $table->string('Subject', 254)->nullable();
            $table->string('UserText', 254)->nullable();
            $table->string('DataCols', 1)->nullable();
            $table->string('DataParams', 1)->nullable();
            $table->string('MsgData', 254)->nullable();
            $table->integer('DraftEntry')->nullable(); // Draft Internal Number
            $table->integer('UserSign')->nullable();
            $table->string('Attachment', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('AltType', 1)->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
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
        Schema::dropIfExists('o_a_l_r_s');
    }
}
