<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFI100STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_i100_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('FormID');
            $table->string('FieldName');
            $table->string('Label');
            $table->string('FieldType');
            $table->string('ColumnWidth');
            $table->string('Visible');
            $table->string('Readonly');
            $table->string('Required');
            $table->string('data')->nullable();
            $table->integer('Location')->nullable();
            $table->string('Position', 1)->nullable()->comment("L=Left, R=Right");
            $table->integer('TabID')->nullable();
            $table->string('ItemText')->nullable();
            $table->string('ItemValue')->nullable();
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
        Schema::dropIfExists('f_i100_s');
    }
}
