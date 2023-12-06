<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOFPRSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_f_p_r_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('absentry', 1)->nullable();
            $table->string('code', 1)->nullable();
            $table->string('Name', 1)->nullable();
            $table->string('f_refdate', 1)->nullable();
            $table->string('t_refdate', 1)->nullable();
            $table->string('f_duedate', 1)->nullable();
            $table->string('t_duedate', 1)->nullable();
            $table->string('f_taxdate', 1)->nullable();
            $table->string('t_taxdate', 1)->nullable();
            $table->string('free2', 1)->nullable();
            $table->string('free3', 1)->nullable();
            $table->string('datasource', 1)->nullable();
            $table->string('usersign', 1)->nullable();
            $table->string('subnum', 1)->nullable();
            $table->string('free', 1)->nullable();
            $table->string('free1', 1)->nullable();
            $table->string('addition', 1)->nullable();
            $table->string('addnum', 1)->nullable();
            $table->string('category', 1)->nullable();
            $table->string('indicator', 1)->nullable();
            $table->string('loginstanc', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('wasstatchd', 1)->nullable();
            $table->string('periodstat', 1)->nullable();
            $table->string('usersign2', 1)->nullable();
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
        Schema::dropIfExists('o_f_p_r_s');
    }
}
