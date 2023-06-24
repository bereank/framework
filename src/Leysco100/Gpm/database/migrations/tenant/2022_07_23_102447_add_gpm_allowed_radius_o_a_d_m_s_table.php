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
        Schema::table('o_a_d_m_s', function (Blueprint $table) {
            $table->integer('enableGateMaximumRadiusRestriction')->default(0)->comment("0=Not Enable, 1=Enable");
            $table->integer('gateMaximumRadius')->default(10)->comment("Allowed Radius GPM");
        });
    }
};
