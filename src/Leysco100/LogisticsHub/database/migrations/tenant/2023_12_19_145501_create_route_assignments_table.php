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
        Schema::create('route_assignments', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->dateTime("Date");
            $table->string("Repeat")->nullable();
            $table->integer("RouteID")
                ->references("id")->on("route_plannings");
            $table->integer("SlpCode")
                ->references("SlpCode")->on("o_s_l_p_s");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_assignments');
    }
};
