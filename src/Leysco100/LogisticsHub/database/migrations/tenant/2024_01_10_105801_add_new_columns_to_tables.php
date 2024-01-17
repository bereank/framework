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
        Schema::table('o_g_p_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_g_p_s', 'Name')) {
                $table->string('Name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('o_g_p_s', 'DocNum')) {
                $table->string('DocNum')->nullable()->after('Name');
            }
            if (!Schema::hasColumn('o_g_p_s', 'UserSign')) {
                $table->string('UserSign')->nullable()->after('DocNum');
            }
            if (!Schema::hasColumn('o_g_p_s', 'OwnerCode')) {
                $table->integer('OwnerCode')->nullable()->after('UserSign');
            }
            if (!Schema::hasColumn('o_g_p_s', 'ObjType')) {
                $table->integer('ObjType')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_g_p_s', 'UpdtFrq')) {
                $table->boolean('UpdtFrq')->default(0)->nullable()->after('ObjType');
            }
        });
        Schema::table('gps_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('gps_locations', 'OwnerCode')) {
                $table->boolean('OwnerCode')->default(0)->nullable()->after('id');
            }
            if (!Schema::hasColumn('gps_locations', 'ObjType')) {
                $table->integer('ObjType')->nullable()->after('OwnerCode');
            }
        });

        Schema::table('e_t_s1_s', function (Blueprint $table) {
            if (!Schema::hasColumn('e_t_s1_s', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('Comment');
            }
            if (!Schema::hasColumn('e_t_s1_s', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('e_t_s1_s', 'address')) {
                $table->string('address')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('e_t_s1_s', 'address')) {
                $table->string('LogDevice')->nullable()->after('address');
            }
        });

        Schema::table('o_c_l_g_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_c_l_g_s', 'OwnerCode')) {
                $table->integer('OwnerCode')->nullable();
            }
            if (!Schema::hasColumn('o_c_l_g_s', 'ObjType')) {
                $table->integer('ObjType')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('o_g_p_s', function (Blueprint $table) {
            $table->dropColumn('UpdtFrq');
            $table->dropColumn('ObjType');
            $table->dropColumn('OwnerCode');
            $table->dropColumn('UserSign');
            $table->dropColumn('DocNum');
            $table->dropColumn('Name');
        });
        Schema::table('gps_locations', function (Blueprint $table) {
            $table->dropColumn('OwnerCode');
            $table->dropColumn('ObjType');
        });
        Schema::table('e_t_s1_s', function (Blueprint $table) {
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
            $table->dropColumn('LogDevice');
            $table->dropColumn('address');
        });

        Schema::table('o_c_l_g_s', function (Blueprint $table) {
            $table->integer('OwnerCode')->nullable();
            $table->integer('ObjType')->nullable();
        });
    }
};
