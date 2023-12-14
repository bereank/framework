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
        Schema::table('c_u_f_d', function (Blueprint $table) {
            if (!Schema::hasColumn('c_u_f_d', 'RTable')) {
                $table->string('RTable')->nullable()->after('NotNull');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RField')) {
                $table->string('RField')->nullable()->after('RTable');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RField')) {
                $table->char('Sys')->nullable()->after('FieldSize');
            }
            if (!Schema::hasColumn('c_u_f_d', 'DispField')) {
                $table->string('DispField')->nullable()->after('RField');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RelUDO')) {
                $table->string('RelUDO', 1)->nullable()->after('DispField');
            }
            if (!Schema::hasColumn('c_u_f_d', 'Action')) {
                $table->string('Action')->nullable()->after('RelUDO');
            }
            if (!Schema::hasColumn('c_u_f_d', 'ValidRule')) {
                $table->string('ValidRule')->nullable()->default(1)->after('Action');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RelSO')) {
                $table->string('RelSO')->nullable()->after('ValidRule');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RThrdPTab')) {
                $table->string('RThrdPTab')->nullable()->after('RelSO');
            }
            if (!Schema::hasColumn('c_u_f_d', 'RThrdPFld')) {
                $table->string('RThrdPFld')->nullable()->after('RThrdPTab');
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
        Schema::table('c_u_f_d', function (Blueprint $table) {
            //
        });
    }
};
