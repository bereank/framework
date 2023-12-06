<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRCT3STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_c_t3_s', function (Blueprint $table) {
            $table->id();
            $table->integer('DocNum')->nullable();
            $table->integer('LineID')->nullable();
            $table->integer('CreditCard')->nullable();
            $table->string('CreditAcct', 15)->nullable();
            $table->string('CrCardNum', 64)->nullable();
            $table->date('CardValid')->nullable();
            $table->string('VoucherNum', 20)->nullable();
            $table->string('OwnerIdNum', 15)->nullable();
            $table->string('OwnerPhone', 15)->nullable();
            $table->string('CrTypeCode', 1)->nullable();
            $table->string('NumOfPmnts', 1)->nullable();
            $table->string('FirstDue', 1)->nullable();
            $table->string('FirstSum', 1)->nullable();
            $table->float('AddPmntSum')->nullable();
            $table->float('CreditSum')->nullable();
            $table->string('CreditCur', 1)->nullable();
            $table->string('CreditRate', 1)->nullable();
            $table->string('ConfNum', 1)->nullable();
            $table->string('CreditType', 1)->nullable()->default("S");
            $table->string('CredPmnts', 1)->nullable();
            $table->string('PlCrdStat', 1)->nullable();
            $table->string('MagnetStr', 1)->nullable();
            $table->string('SpiltCred', 1)->nullable()->default("N");
            $table->string('ConsolNum', 1)->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->string('U_MpesaRef')->nullable();
            $table->string('U_MpesaTxnNo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_c_t3_s');
    }
}
