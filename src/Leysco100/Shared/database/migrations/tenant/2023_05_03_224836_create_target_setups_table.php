<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_setups', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('SlpCode')->nullable();
            $table->date('TfromDate')->nullable();
            $table->date('TtoDate')->nullable();
            $table->string('RecurPat', 1)->nullable();
            $table->string('Comment', 200)->nullable();
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->string('Status', 1)->default('O')->comment('O = open , C = closed.');
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
        Schema::dropIfExists('target_setups');
    }
};
