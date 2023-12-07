<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFTR100STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_t_r100_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('FormID');
            $table->integer('TabID')->nullable();
            $table->string('FieldType');
            $table->string('data')->nullable();
            $table->string('itemText')->nullable();
            $table->string('itemValue')->nullable();
            $table->string('ColumnWidth');
            $table->string('text');
            $table->string('value');
            $table->integer('width');
            $table->string('Visible');
            $table->string('modalVisible');
            $table->string('readonly');
            $table->string('ClickEvent')->nullable();
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
        Schema::dropIfExists('f_t_r100_s');
    }
}
