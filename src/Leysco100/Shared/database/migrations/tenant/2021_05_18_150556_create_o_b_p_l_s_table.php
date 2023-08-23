<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOBPLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_b_p_l_s', function (Blueprint $table) {
            $table->id();
            $table->string('BPLId')->nullable();
            $table->string('BPLName', 100)->nullable();
            $table->string('BPLFrName', 1)->nullable();
            $table->string('VATRegNum', 1)->nullable();
            $table->string('RepName', 1)->nullable();
            $table->string('Industry', 50)->nullable();
            $table->string('Business', 1)->nullable();
            $table->string('Address', 254)->nullable();
            $table->string('AddressFr', 1)->nullable();
            $table->string('MainBPL', 1)->default("N");
            $table->string('TxOffcNo', 1)->nullable();
            $table->string('Disabled', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->integer('DflCust')->nullable();
            $table->integer('DflVendor')->nullable();
            $table->integer('DflWhs')->nullable();
            $table->integer('DflTaxCode')->nullable();
            $table->string('RevOffice', 1)->nullable();
            $table->string('TaxIdNum', 1)->nullable();
            $table->string('TaxIdNum2', 1)->nullable();
            $table->string('TaxIdNum3', 1)->nullable();
            $table->string('AddtnlId', 1)->nullable();
            $table->string('CompNature', 1)->nullable();
            $table->string('EconActT', 1)->nullable();
            $table->string('CredCOrig', 1)->nullable();
            $table->string('IPIPeriod', 1)->nullable();
            $table->string('CoopAssocT', 1)->nullable();
            $table->string('PrefState', 1)->nullable();
            $table->string('ProfTax', 1)->nullable();
            $table->string('CompQualif', 1)->nullable();
            $table->string('DeclType', 1)->nullable();
            $table->string('AddrType', 1)->nullable();
            $table->string('Street', 1)->nullable();
            $table->string('StreetNo', 1)->nullable();
            $table->string('Building', 1)->nullable();
            $table->string('ZipCode', 1)->nullable();
            $table->string('Block', 1)->nullable();
            $table->string('City', 1)->nullable();
            $table->string('State', 1)->nullable();
            $table->string('County', 1)->nullable();
            $table->string('Country', 1)->nullable();
            $table->string('PmtClrAct', 1)->nullable();
            $table->string('CommerReg', 1)->nullable();
            $table->string('DateOfInc', 1)->nullable();
            $table->string('SPEDProf', 1)->nullable();
            $table->string('EnvTypeNFe', 1)->nullable();
            $table->string('Opt4ICMS', 1)->nullable();
            $table->string('AliasName', 1)->nullable();
            $table->string('GlblLocNum', 1)->nullable();
            $table->string('TaxRptFrm', 1)->nullable();
            $table->string('Suframa', 1)->nullable();
            $table->string('DfltResWhs', 1)->nullable();
            $table->string('SnapshotId', 1)->nullable();
            $table->integer('LocationCode')->nullable();
            $table->string('ExtRef')->nullable()->comment("External Ref");
            $table->string('PayBill')->nullable();
            $table->string('GLAccount')->nullable();
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
        Schema::dropIfExists('o_b_p_l_s');
    }
}
