<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCPRSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_p_r_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CntctCode', 1)->nullable();
            $table->integer('CardCode')
                ->references('id')->on('o_c_d_s')->nullable();
            $table->string('Name', 50);
            $table->string('Position', 50)->nullable();
            $table->string('Address', 100)->nullable();
            $table->string('Tel1', 20)->nullable();
            $table->string('Tel2', 20)->nullable();
            $table->string('Cellolar', 1)->nullable();
            $table->string('Fax', 1)->nullable();
            $table->string('E_MailL', 1)->nullable();
            $table->string('Pager', 1)->nullable();
            $table->string('Notes1', 1)->nullable();
            $table->string('Notes2', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->string('Password', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('BirthPlace', 1)->nullable();
            $table->string('BirthDate', 1)->nullable();
            $table->string('Gender', 1)->default('E');
            $table->string('Profession', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('updateTime', 1)->nullable();
            $table->string('Title', 1)->nullable();
            $table->string('BirthCity', 1)->nullable();
            $table->string('Active', 1)->default('Y');
            $table->string('FirstName', 1)->nullable();
            $table->string('MiddleName', 1)->nullable();
            $table->string('LastName', 1)->nullable();
            $table->string('BirthState', 1)->nullable();
            $table->string('ResidCity', 1)->nullable();
            $table->string('ResidCntry', 1)->nullable();
            $table->string('ResidState', 1)->nullable();
            $table->string('NFeRcpn', 1)->nullable();
            $table->string('EmlGrpCode', 1)->nullable();
            $table->string('BlockComm', 1)->nullable();
            $table->string('FiscalCode', 1)->nullable();
            $table->string('CtyPrvsYr', 1)->nullable();
            $table->string('SttPrvsYr', 1)->nullable();
            $table->string('CtyCdPrvsY', 1)->nullable();
            $table->string('CtyCurYr', 1)->nullable();
            $table->string('SttCurYr', 1)->nullable();
            $table->string('CtyCdCurYr', 1)->nullable();
            $table->string('NotResdSch', 1)->nullable();
            $table->string('CtyFsnCode', 1)->nullable();
            $table->string('NaturalPer', 1)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->string('CreateTS', 1)->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
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
        Schema::dropIfExists('o_c_p_r_s');
    }
}
