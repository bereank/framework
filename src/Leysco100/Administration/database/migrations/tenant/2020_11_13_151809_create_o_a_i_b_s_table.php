<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOAIBSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_a_i_b_s', function (Blueprint $table) {
            $table->id();
            $table->integer('AlertCode')->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('Opened', 1)->default('N');
            $table->timestamp('RecDate')->nullable();
            $table->string('RecTime')->nullable();
            $table->string('WasRead', 1)->default('N');
            $table->string('Deleted', 1)->default('N');
            $table->string('Failed', 1)->default('N');
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
        Schema::dropIfExists('o_a_i_b_s');
    }
}
