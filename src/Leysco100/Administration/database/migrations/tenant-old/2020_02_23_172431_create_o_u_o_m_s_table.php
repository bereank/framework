<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOUOMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_u_o_m_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('UomCode', 20)->unique();
            $table->string('UomName', 100)->nullable();
            $table->string('Locked', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->string('Length1', 1)->nullable();
            $table->string('Len1Unit', 1)->nullable();
            $table->string('length2', 1)->nullable();
            $table->string('Len2Unit', 1)->nullable();
            $table->string('Width1', 1)->nullable();
            $table->string('Wdth1Unit', 1)->nullable();
            $table->string('Width2', 1)->nullable();
            $table->string('Wdth2Unit', 1)->nullable();
            $table->string('Height1', 1)->nullable();
            $table->string('Hght1Unit', 1)->nullable();
            $table->string('Height2', 1)->nullable();
            $table->string('Hght2Unit', 1)->nullable();
            $table->string('Volume', 1)->nullable();
            $table->string('VolUnit', 1)->nullable();
            $table->string('Weight1', 1)->nullable();
            $table->string('WghtUnit', 1)->nullable();
            $table->string('Weight2', 1)->nullable();
            $table->string('Wght2Unit', 1)->nullable();
            $table->string('IntSymbol', 1)->nullable();
            $table->string('EwbUnit', 1)->nullable();
            $table->string('ExtRef')->nullable(); //Name
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
        Schema::dropIfExists('o_u_o_m_s');
    }
}
