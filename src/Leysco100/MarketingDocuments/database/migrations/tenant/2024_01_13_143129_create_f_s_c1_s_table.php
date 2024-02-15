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
            $table->integer("DocId");
            $table->integer('ObjCode')->nullable();
            $table->integer("DocEntry");
            $table->string("U_ControlCode")->nullable();
            $table->string("U_RelatedInv")->nullable();
            $table->string("U_CUInvoiceNum")->nullable();
            $table->string("U_QRCode")->nullable();
            $table->string("U_QrLocation")->nullable();
            $table->string("U_ReceiptNo")->nullable();
            $table->string("U_CommitedTime")->nullable();
            $table->integer("statusCode")->comment("0 for success, 1 for failed");
            $table->text("message");
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
