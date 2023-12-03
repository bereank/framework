<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOWSTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_w_s_t_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('WstCode', 10)->nullable();
            $table->string('Name', 100)->nullable();
            $table->string('Remarks', 100)->nullable();
            $table->integer('MaxReqr')->nullable();
            $table->integer('UserSign')->nullable();
            $table->integer('MaxRejReqr')->nullable();
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
        Schema::dropIfExists('o_w_s_t_s');
    }
}
