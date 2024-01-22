<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITB1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_t_b1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ItmsGrpCod')->nullable();
            $table->string('Field')->nullable();
            $table->string('Description')->nullable();
            $table->integer('AcctCode')->nullable();
            $table->string('AcctName')->nullable();
            $table->string('Category', 1)->nullable(); // S-Sales, P-Purchase, G-General, I-Inventory, R-Resource
            $table->string('OtherCty', 1)->nullable(); // G-General,T-Tax
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
        Schema::dropIfExists('i_t_b1_s');
    }
}
