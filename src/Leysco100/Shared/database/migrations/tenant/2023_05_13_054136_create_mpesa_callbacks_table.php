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
        Schema::create('mpesa_callback', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("transactionData");
            $table->string("transactionId")->nullable();
            $table->string("transactionAmount")->nullable();
            $table->string("transactionTime")->nullable();
            $table->string("transactionMpesaRef")->nullable();
            $table->string("transactionPhoneNumber")->nullable();
            $table->string("transactionRefNumber")->nullable();
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
        Schema::dropIfExists('mpesa_callback');
    }
};
