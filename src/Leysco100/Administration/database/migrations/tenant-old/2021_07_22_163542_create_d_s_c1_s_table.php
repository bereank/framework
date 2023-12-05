<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDSC1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_s_c1_s', function (Blueprint $table) {
            $table->id();
            $table->string('BankCode')->unique();
            $table->string('Account');
            $table->string('Branch')->comment("branch");
            $table->string('GLAccount')->comment("Gl Account");
            $table->string('AcctName');
            $table->string('BankKey')->comment(" Bank Account  Key");
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
        Schema::dropIfExists('d_s_c1_s');
    }
}
