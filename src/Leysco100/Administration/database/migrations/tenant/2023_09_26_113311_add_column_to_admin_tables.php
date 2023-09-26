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

        Schema::table('o_u_d_g_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_u_d_g_s', 'AddToFavourites')) {
                $table->boolean('AddToFavourites')->default(0)->after('CogsOcrCo5');
            }
        });
        Schema::table('o_a_d_m_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_a_d_m_s', 'DoFilter')) {
                $table->boolean('DoFilter')->default(0);
            }
        });
       
        Schema::table('a_p_d_i_s', function (Blueprint $table) {
            if (!Schema::hasColumn('a_p_d_i_s', 'ObjAcronym')) {
                $table->string('ObjAcronym')->nullable()->after('ObjectID');
            }
            if (!Schema::hasColumn('a_p_d_i_s', 'DocType')) {
                $table->integer('DocType')->nullable()->after('hasExtApproval');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert the modifications made in the up() method
        Schema::table('o_u_d_g_s', function (Blueprint $table) {
            $table->dropColumn('AddToFavourites');
        });

        Schema::table('a_p_d_i_s', function (Blueprint $table) {
            $table->dropColumn('ObjAcronym');
            $table->dropColumn('DocType');
        });
    }
};
