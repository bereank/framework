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
            $table->integer("InvoiceId");
            $table->integer("statusCode")->comment("0 for success, 1 for failed");
            $table->text("message");
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
