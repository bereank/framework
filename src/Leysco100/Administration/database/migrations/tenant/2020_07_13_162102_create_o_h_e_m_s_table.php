<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOHEMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_h_e_m_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('empID')->nullable();
            $table->string('lastName')->nullable();
            $table->string('firstName')->nullable();
            $table->string('middleName')->nullable();
            $table->string('sex')->nullable();
            $table->string('jobTitle')->nullable();
            $table->string('type', 1)->nullable();
            $table->string('dept')->nullable();
            $table->string('branch')->nullable();
            $table->string('workStreet')->nullable();
            $table->string('workBlock')->nullable();
            $table->string('workZip')->nullable();
            $table->string('workCity')->nullable();
            $table->string('workCounty', 191)->nullable();
            $table->string('workCountr', 191)->nullable();
            $table->string('workState', 191)->nullable();
            $table->string('manager', 191)->nullable();
            $table->string('userId', 191)->nullable();
            $table->string('salesPrson', 191)->nullable();
            $table->string('officeTel', 191)->nullable();
            $table->string('officeExt', 191)->nullable();
            $table->string('mobile', 191)->nullable();
            $table->string('pager', 191)->nullable();
            $table->string('homeTel', 191)->nullable();
            $table->string('fax', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('startDate', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('salary', 100)->nullable();
            $table->string('salaryUnit', 100)->nullable();
            $table->string('emplCost', 100)->nullable();
            $table->string('empCostUnt', 100)->nullable();
            $table->string('termDate', 100)->nullable();
            $table->string('termReason', 100)->nullable();
            $table->string('bankCode', 100)->nullable();
            $table->string('bankBranch', 100)->nullable();
            $table->string('bankBranNo', 100)->nullable();
            $table->string('bankAcount', 100)->nullable();
            $table->string('homeStreet', 100)->nullable();
            $table->string('homeBlock', 100)->nullable();
            $table->string('homeZip', 100)->nullable();
            $table->string('homeCity', 100)->nullable();
            $table->string('homeCounty', 100)->nullable();
            $table->string('homeCountr', 100)->nullable();
            $table->string('homeState', 100)->nullable();
            $table->string('birthDate', 100)->nullable();
            $table->string('brthCountr', 1)->nullable();
            $table->string('martStatus', 1)->nullable();
            $table->string('nChildren', 1)->nullable();
            $table->string('govID', 20)->nullable();
            $table->string('citizenshp', 20)->nullable();
            $table->string('passportNo', 20)->nullable();
            $table->string('passportEx', 20)->nullable();
            $table->string('picture', 20)->nullable();
            $table->string('remark', 20)->nullable();
            $table->string('attachment', 20)->nullable();
            $table->string('salaryCurr', 20)->nullable();
            $table->string('empCostCur', 20)->nullable();
            $table->string('WorkBuild', 20)->nullable();
            $table->string('HomeBuild', 20)->nullable();
            $table->string('position', 20)->nullable();
            $table->string('AtcEntry', 20)->nullable();
            $table->string('AddrTypeW', 20)->nullable();
            $table->string('AddrTypeH', 20)->nullable();
            $table->string('StreetNoW', 20)->nullable();
            $table->string('StreetNoH', 20)->nullable();
            $table->string('DispMidNam', 20)->nullable();
            $table->string('NamePos', 20)->nullable();
            $table->string('DispComma', 20)->nullable();
            $table->string('CostCenter', 20)->nullable();
            $table->string('CompanyNum', 20)->nullable();
            $table->string('VacPreYear', 20)->nullable();
            $table->string('VacCurYear', 20)->nullable();
            $table->string('MunKey', 1)->nullable();
            $table->string('TaxClass', 1)->nullable();
            $table->string('InTaxLiabi', 1)->nullable();
            $table->string('EmTaxCCode', 1)->nullable();
            $table->string('RelPartner', 1)->nullable();
            $table->string('ExemptAmnt', 1)->nullable();
            $table->string('ExemptUnit', 1)->nullable();
            $table->string('AddiAmnt', 1)->nullable();
            $table->string('AddiUnit', 1)->nullable();
            $table->string('TaxOName', 1)->nullable();
            $table->string('TaxONum', 1)->nullable();
            $table->string('HeaInsName', 1)->nullable();
            $table->string('HeaInsCode', 1)->nullable();
            $table->string('HeaInsType', 1)->nullable();
            $table->string('SInsurNum', 1)->nullable();
            $table->string('StatusOfP', 1)->nullable();
            $table->string('StatusOfE', 1)->nullable();
            $table->string('BCodeDateV', 1)->nullable();
            $table->string('DevBAOwner', 1)->nullable();
            $table->string('FNameSP', 1)->nullable();
            $table->string('SurnameSP', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('UserSign')->nullable();
            $table->string('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('PersGroup', 1)->nullable();
            $table->string('JTCode', 1)->nullable();
            $table->string('ExtEmpNo', 1)->nullable();
            $table->string('BirthPlace', 1)->nullable();
            $table->string('PymMeth', 1)->nullable();
            $table->string('ExemptCurr', 1)->nullable();
            $table->string('AddiCurr', 1)->nullable();
            $table->string('STDCode', 1)->nullable();
            $table->string('FatherName', 1)->nullable();
            $table->string('CPF', 1)->nullable();
            $table->string('CRC', 1)->nullable();
            $table->string('ContResp', 1)->nullable();
            $table->string('RepLegal', 1)->nullable();
            $table->string('DirfDeclar', 1)->nullable();
            $table->string('UF_CRC', 1)->nullable();
            $table->string('IDType', 1)->nullable();
            $table->string('Active', 1)->nullable();
            $table->string('BPLId', 100)->nullable();
            $table->string('ManualNUM', 20)->nullable();
            $table->string('PassIssue', 20)->nullable();
            $table->string('PassIssuer', 20)->nullable();
            $table->string('QualCode', 20)->nullable();
            $table->string('PRWebAccss', 20)->nullable();
            $table->string('PrePRWeb', 20)->nullable();
            $table->string('BPLink', 20)->nullable();
            $table->string('NaturalPer', 20)->nullable();
            $table->string('DPPStatus', 20)->nullable();
            $table->string('EnRligion', 20)->nullable();
            $table->string('EnRligionP', 20)->nullable();
            $table->string('EncryptIV', 20)->nullable();
            $table->string('EnGovID', 20)->nullable();
            $table->string('EnPassport', 20)->nullable();
            $table->string('CreateDate', 20)->nullable();
            $table->string('CreateTS', 20)->nullable();
            $table->string('UpdateTS', 20)->nullable();
            $table->string('EnInsurNum', 20)->nullable();
            $table->string('EnBnkAcct', 20)->nullable();
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
        Schema::dropIfExists('o_h_e_m_s');
    }
}
