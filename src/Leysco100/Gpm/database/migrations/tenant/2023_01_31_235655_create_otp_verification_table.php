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
        Schema::create('otp_verification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('UserSign');
            $table->foreignId('GateId')->nullable();
            $table->string('phone_number');
            $table->string('otp_code');
            $table->integer('status')->default(3)->comment('0 = attempt verification, 1= successfully verified 2 = verification failed, 3 = otp code expired');
            $table->string('sms_response')->nullable();
            $table->dateTime('expires_at');
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
        Schema::dropIfExists('otp_verification');
    }
};
