<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('f_s_c1_s', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("DocEntry");
            $table->string("ControlCode")->nullable();
            $table->string("RelatedInvNum")->nullable();
            $table->string("BaseInvNum")->nullable();
            $table->string("RAuthorityURL")->nullable();
            $table->string("CUInvNum")->nullable();
            $table->string("ReceiptNo")->nullable();
            $table->string("DeviceSerialNo")->nullable();
            $table->text("message");
            $table->integer('Status')->nullable()->comment("0 for success, 1 for failed");
            $table->string('Canceled', 1)->default('N')->nullable();
            $table->integer('LogInst')->nullable();
            $table->text("cache")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_s_c1_s');
    }
};
