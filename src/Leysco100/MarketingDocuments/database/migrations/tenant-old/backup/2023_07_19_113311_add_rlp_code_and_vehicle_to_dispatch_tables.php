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

        Schema::table('d_e_l_c_o_n_f1', function (Blueprint $table) {
            if (!Schema::hasColumn('d_e_l_c_o_n_f1', 'RlpCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('d_e_l_c_o_n_f1', 'vehicle_id')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('d_e_l_c_o_n_f1', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
        });
        Schema::table('d_i_s_p_a_s_s1_s', function (Blueprint $table) {
            if (!Schema::hasColumn('d_i_s_p_a_s_s1_s', 'RlpCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_a_s_s1_s', 'vehicle_id')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_a_s_s1_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
        });

        Schema::table('d_i_s_p_n_o_t1_s', function (Blueprint $table) {
            if (!Schema::hasColumn('d_i_s_p_n_o_t1_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_n_o_t1_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_n_o_t1_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
        });

        Schema::table('d_i_s_p_r_e_t1_s', function (Blueprint $table) {
            if (!Schema::hasColumn('d_i_s_p_r_e_t1_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_r_e_t1_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('d_i_s_p_r_e_t1_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
        });

        Schema::table('o_d_e_l_c_o_n_f', function (Blueprint $table) {
            if (!Schema::hasColumn('o_d_e_l_c_o_n_f', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('o_d_e_l_c_o_n_f', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('o_d_e_l_c_o_n_f', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
            if (!Schema::hasColumn('o_d_e_l_c_o_n_f', 'Comments')) {
                $table->string('Comments')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_d_e_l_c_o_n_f', 'Attachment')) {
                $table->string('Attachment')->nullable()->after('Comments');
            }
        });

        Schema::table('o_d_i_s_p_a_s_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_d_i_s_p_a_s_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_a_s_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_a_s_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
            if (!Schema::hasColumn('o_d_i_s_p_a_s_s', 'Comments')) {
                $table->string('Comments')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_a_s_s', 'Attachment')) {
                $table->string('Attachment')->nullable()->after('Comments');
            }
        });

        Schema::table('o_d_i_s_p_n_o_t_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_d_i_s_p_n_o_t_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_n_o_t_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_n_o_t_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
            if (!Schema::hasColumn('o_d_i_s_p_n_o_t_s', 'Comments')) {
                $table->string('Comments')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_n_o_t_s', 'Attachment')) {
                $table->string('Attachment')->nullable()->after('Comments');
            }
        });

        Schema::table('o_d_i_s_p_r_e_t_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_d_i_s_p_r_e_t_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_r_e_t_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_r_e_t_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
            if (!Schema::hasColumn('o_d_i_s_p_r_e_t_s', 'Comments')) {
                $table->string('Comments')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_d_i_s_p_r_e_t_s', 'Attachment')) {
                $table->string('Attachment')->nullable()->after('Comments');
            }
        });
        Schema::table('o_i_n_v_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_i_n_v_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('o_i_n_v_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('o_i_n_v_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
            if (!Schema::hasColumn('o_i_n_v_s', 'Comments')) {
                $table->string('Comments')->nullable()->after('OwnerCode');
            }
            if (!Schema::hasColumn('o_i_n_v_s', 'Attachment')) {
                $table->string('Attachment')->nullable()->after('Comments');
            }
        });
        Schema::table('i_n_v1_s', function (Blueprint $table) {
            if (!Schema::hasColumn('i_n_v1_s', 'ClgCode')) {
                $table->string('RlpCode')->nullable()->after('SlpCode');
            }
            if (!Schema::hasColumn('i_n_v1_s', 'ClgCode')) {
                $table->integer('vehicle_id')->nullable()->after('RlpCode');
            }
            if (!Schema::hasColumn('i_n_v1_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
            }
        });
        Schema::table('o_c_l_g_s', function (Blueprint $table) {
            if (!Schema::hasColumn('o_c_l_g_s', 'ClgCode')) {
                $table->string('ClgCode')->nullable()->after('vehicle_id');
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
        Schema::table('d_e_l_c_o_n_f1', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('d_i_s_p_a_s_s1_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('d_i_s_p_n_o_t1_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('d_i_s_p_r_e_t1_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('o_d_e_l_c_o_n_f', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('o_d_i_s_p_a_s_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('o_d_i_s_p_n_o_t_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });

        Schema::table('o_d_i_s_p_r_e_t_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });
        Schema::table('o_i_n_v_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });
        Schema::table('i_n_v1_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
            $table->dropColumn('vehicle_id');
            $table->dropColumn('ClgCode');
        });
        Schema::table('o_c_l_g_s', function (Blueprint $table) {
            $table->dropColumn('RlpCode');
        });
    }
};
