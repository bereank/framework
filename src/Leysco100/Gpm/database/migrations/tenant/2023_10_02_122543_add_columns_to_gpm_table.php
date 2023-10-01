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

            if (!Schema::hasColumn('o_a_d_m_s', 'PswdChangeOnReset')) {
                $table->boolean('PswdChangeOnReset')->default(false);
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'HasOtpVerification')) {
                $table->boolean('HasOtpVerification')->default(false);
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'ExtBucketAccessID')) {
                $table->string('ExtBucketAccessID', 255)->nullable();
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'ExtBucketSecretKey')) {
                $table->string('ExtBucketSecretKey', 255)->nullable();
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'ExtBucketDestDir')) {
                $table->string('ExtBucketDestDir', 255)->nullable();
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'ExtBucket')) {
                $table->string('ExtBucket', 255)->nullable();
            }
            if (!Schema::hasColumn('o_a_d_m_s', 'ExtBucketRegion')) {
                $table->string('ExtBucketRegion', 255)->nullable();
            }
            if (!Schema::hasColumn('g_m_s1_s', 'OwnerCode')) {
                $table->string('OwnerCode', 255)->nullable();
            }
            if (!Schema::hasColumn('o_g_m_s', 'OwnerCode')) {
                $table->string('OwnerCode', 255)->nullable();
            }
            if (!Schema::hasColumn('gates', 'OwnerCode')) {
                $table->string('OwnerCode', 255)->nullable();
            }
            if (!Schema::hasColumn('back_up_mode_lines', 'OwnerCode')) {
                $table->string('OwnerCode', 255)->nullable();
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
        Schema::table('o_a_d_m_s', function (Blueprint $table) {
            $table->dropColumn(['ExtBucketAccessID', 'ExtBucketSecretKey', 'ExtBucketUsrName', 'ExtBucketPath']);
        });
    }
};
