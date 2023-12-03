<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWDD1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_d_d1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('WddCode')->nullable();
            $table->integer('StepCode')->nullable(); //Stage Key
            $table->integer('UserID')->nullable(); //    Authorizer Code
            $table->string('Status', 1)->nullable(); //N=Rejected, W=Pending, Y=Approved
            $table->string('Remarks', 254)->nullable();
            $table->integer('UserSign')->nullable();
            $table->date('CreateDate', 1)->nullable();
            $table->string('CreateTime', 1)->nullable();
            $table->datetime('UpdateDate')->nullable();
            $table->string('UpdateTime')->nullable();
            $table->integer('SortId')->nullable();
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
        Schema::dropIfExists('w_d_d1_s');
    }
}
