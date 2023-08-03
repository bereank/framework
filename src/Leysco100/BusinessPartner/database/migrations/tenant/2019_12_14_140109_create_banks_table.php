<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('BankCode')->unique();
            $table->string('BankName');
            $table->string('DfltAcct')->comment("account for outgoing check");
            $table->string('DfltBranch')->comment("Branch for ougoing Check");
            $table->string('Locked');
            $table->string('DfltActKey')->comment("Default Bank Account  Key");
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence for Absolute entry");
            $table->integer('country_id')->references('id')->on('countries')->nullable();
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
        Schema::dropIfExists('banks');
    }
}
