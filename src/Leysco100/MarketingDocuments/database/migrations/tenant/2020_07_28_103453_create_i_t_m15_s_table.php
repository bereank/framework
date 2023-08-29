<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITM15STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_t_m15_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ItemCode'); // iTEM cODE
            $table->integer('ItmsTypCod'); // OITG
            $table->integer('QryGroup')->nullable(); // ITG1
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
        Schema::dropIfExists('i_t_m15_s');
    }
}
