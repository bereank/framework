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
            $table->string('jobTitle', 1)->nullable();
            $table->string('type', 1)->nullable();
            $table->string('dept')->nullable();
            $table->string('branch')->nullable();
            $table->string('workStreet', 1)->nullable();
            $table->string('workBlock', 1)->nullable();
            $table->string('workZip', 1)->nullable();
            $table->string('workCity', 1)->nullable();
            $table->string('workCounty', 1)->nullable();
            $table->string('workCountr', 1)->nullable();
            $table->string('workState', 1)->nullable();
            $table->string('manager', 1)->nullable();
            $table->string('userId', 1)->nullable();
            $table->string('salesPrson', 1)->nullable();
            $table->string('officeTel', 1)->nullable();
            $table->string('officeExt', 1)->nullable();
            $table->string('mobile', 1)->nullable();
            $table->string('pager', 1)->nullable();
            $table->string('homeTel', 1)->nullable();
            $table->string('fax', 1)->nullable();
            $table->string('email', 1)->nullable();
            $table->string('startDate', 1)->nullable();
            $table->string('status', 1)->nullable();
            $table->string('salary', 1)->nullable();
            $table->string('salaryUnit', 1)->nullable();
            $table->string('emplCost', 1)->nullable();
            $table->string('empCostUnt', 1)->nullable();
            $table->string('termDate', 1)->nullable();
            $table->string('termReason', 1)->nullable();
            $table->string('bankCode', 1)->nullable();
            $table->string('bankBranch', 1)->nullable();
            $table->string('bankBranNo', 1)->nullable();
            $table->string('bankAcount', 1)->nullable();
            $table->string('homeStreet', 1)->nullable();
            $table->string('homeBlock', 1)->nullable();
            $table->string('homeZip', 1)->nullable();
            $table->string('homeCity', 1)->nullable();
            $table->string('homeCounty', 1)->nullable();
            $table->string('homeCountr', 1)->nullable();
            $table->string('homeState', 1)->nullable();
            $table->string('birthDate', 1)->nullable();
            $table->string('brthCountr', 1)->nullable();
            $table->string('martStatus', 1)->nullable();
            $table->string('nChildren', 1)->nullable();
            $table->string('govID', 1)->nullable();
            $table->string('citizenshp', 1)->nullable();
            $table->string('passportNo', 1)->nullable();
            $table->string('passportEx', 1)->nullable();
            $table->string('picture', 1)->nullable();
            $table->string('remark', 1)->nullable();
            $table->string('attachment', 1)->nullable();
            $table->string('salaryCurr', 1)->nullable();
            $table->string('empCostCur', 1)->nullable();
            $table->string('WorkBuild', 1)->nullable();
            $table->string('HomeBuild', 1)->nullable();
            $table->string('position', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('AddrTypeW', 1)->nullable();
            $table->string('AddrTypeH', 1)->nullable();
            $table->string('StreetNoW', 1)->nullable();
            $table->string('StreetNoH', 1)->nullable();
            $table->string('DispMidNam', 1)->nullable();
            $table->string('NamePos', 1)->nullable();
            $table->string('DispComma', 1)->nullable();
            $table->string('CostCenter', 1)->nullable();
            $table->string('CompanyNum', 1)->nullable();
            $table->string('VacPreYear', 1)->nullable();
            $table->string('VacCurYear', 1)->nullable();
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
            $table->string('ManualNUM', 1)->nullable();
            $table->string('PassIssue', 1)->nullable();
            $table->string('PassIssuer', 1)->nullable();
            $table->string('QualCode', 1)->nullable();
            $table->string('PRWebAccss', 1)->nullable();
            $table->string('PrePRWeb', 1)->nullable();
            $table->string('BPLink', 1)->nullable();
            $table->string('NaturalPer', 1)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->string('EnRligion', 1)->nullable();
            $table->string('EnRligionP', 1)->nullable();
            $table->string('EncryptIV', 1)->nullable();
            $table->string('EnGovID', 1)->nullable();
            $table->string('EnPassport', 1)->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->string('CreateTS', 1)->nullable();
            $table->string('UpdateTS', 1)->nullable();
            $table->string('EnInsurNum', 1)->nullable();
            $table->string('EnBnkAcct', 1)->nullable();
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
