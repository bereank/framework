<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOPLNSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_p_l_n_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ListNum')->nullable();
            $table->string('ListName')->nullable();
            $table->float('Factor')->nullable();
            $table->integer('BASE_NUM')->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('isGrossPrc')->default('N');
            $table->integer('LogInstance')->default(0);
            $table->integer('UserSign2')->nullable();
            $table->string('UpdateDate')->nullable();
            $table->string('ValidFor')->default('Y');
            $table->string('ValidFrom')->nullable();
            $table->string('ValidTo')->nullable();
            $table->string('PrimCurr')->nullable();
            $table->string('AddCurr1')->nullable();
            $table->string('AddCurr2')->nullable();
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
        Schema::dropIfExists('o_p_l_n_s');
    }
}
