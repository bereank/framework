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
        Schema::table('o_u_d_g_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_u_d_g_s', 'DftBinLoc')) {
                $table->string('DftBinLoc')->nullable()->after('AddToFavourites');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'ClockIn')) {
                $table->string('ClockIn')->nullable()->after('DftBinLoc');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'EtstCode')) {
                $table->string('EtstCode')->nullable()->after('ClockIn');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'RouteID')) {
                $table->integer('RouteID')->nullable()->after('EtstCode');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'RouteActive')) {
                $table->integer('RouteActive')->nullable()->after('RouteID');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'SellFromBin')) {
                $table->boolean('SellFromBin')->default(0)->nullable()->after('RouteActive');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'GpsActive')) {
                $table->boolean('GpsActive')->default(0)->nullable()->after('SellFromBin');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'GpsSetUpID')) {
                $table->integer('GpsSetUpID')->nullable()->after('GpsActive');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'EdtItmPrc')) {
                $table->boolean('EdtItmPrc')->default(0)->nullable()->after('GpsSetUpID');
            }
            if (!Schema::hasColumn('o_u_d_g_s', 'MultiLogin')) {
                $table->boolean('MultiLogin')->default(0)->nullable()->after('EdtItmPrc');
            }
           
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('o_u_d_g_s', function (Blueprint $table) {
            $table->dropColumn('DftBinLoc');
            $table->dropColumn('EtstCode');
            $table->dropColumn('ClockIn');
            $table->dropColumn('RouteID');
            $table->dropColumn('RouteActive');
            $table->dropColumn('SellFromBin');
            $table->dropColumn('MultiLogin');
        });
    }
};
