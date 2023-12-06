<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOVTGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_v_t_g_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Code')->nullable();
            $table->string('Name')->nullable();
            $table->integer('Rate')->default(0);
            $table->date('EffecDate')->nullable();
            $table->string('Category', 1)->default('O'); //I=Input Tax, O=Output Tax
            $table->integer('Account')->references('id')->on('chart_of_accounts');
            $table->string('Locked', 1)->default('N'); //N=No, Y=Yes
            $table->string('DataSource', 1)->default('N'); //A=Doc. Generation Wizard, D=Restore Wizard, I=Interface, M=Import, N=Unknown, O=DI API, P=Partner Implementation, T=Year Transfer, U=Update
            $table->string('UserSign', 1)->nullable();
            $table->string('IsEC')->default('N'); //N=No, Y=Yes
            $table->string('Indicator', 1)->nullable();
            $table->string('AcqstnRvrs', 1)->default('N'); //N=No, Y=Yes
            $table->integer('NonDedct')->default(0);
            $table->string('AcqsTax')->references('id')->on('chart_of_accounts');
            $table->string('GoddsShip', 1)->nullable();
            $table->string('NonDedAcc')->references('id')->on('chart_of_accounts');
            $table->string('DeferrAcc')->references('id')->on('chart_of_accounts');
            $table->string('EquVatPr')->default(0);
            $table->string('ReportCode')->nullable();
            $table->string('FixdAssts', 1)->default('N'); //N=No, Y=Yes
            $table->string('CalcMethod', 1)->default('R'); //F=Fixed, R=Rate
            $table->string('TaxType', 1)->default('V'); //S=Stamp, V=VAT
            $table->string('FixedAmnt', 1)->nullable();
            $table->string('ExtCode', 1)->nullable();
            $table->string('Correction', 1)->default('N'); //N=No, Y=Yes
            $table->string('VatCrctn', 1)->nullable();
            $table->string('RetVatCode', 1)->nullable();
            $table->string('RepType', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('TaxCtgr', 1)->default('E'); //A=Taxable - Article 162, B=Taxable - Article 173 point 5, C=Taxable - Construction, D=Taxable - Expected Confirmation, E=Excluded, F=Taxable - Gross, G=Gross Profit, H=Excluded Art. 15, I=Taxable - Import, J=Not Subject, K=Paid in other EU country, L=Taxable - Late Export, M=Taxable - Article 151 point 1, N=Not Taxable, O=Taxable - Not Con
            $table->string('EquAccount')->references('id')->on('chart_of_accounts');
            $table->string('UserSign2', 1)->nullable();
            $table->string('IsIGIC', 1)->default('N'); //N=No, Y=Yes
            $table->string('ServSupply', 1)->nullable();
            $table->string('Inactive', 1)->nullable(); //N=No, Y=Yes
            $table->string('TaxCtgrBL', 1)->default('E'); //E=Excluded, N=Not Taxable, S=Non-Subject, T=Taxable, X=Exempt
            $table->string('R349Code', 11)->default('0'); //0, 1=E, 2=A, 3=T, 4=S, 5=I, 6=M, 7=H
            $table->string('VatRevAcc')->references('id')->on('chart_of_accounts');
            $table->string('CashDisAcc')->references('id')->on('chart_of_accounts');
            $table->string('DpmTaxOAcc')->references('id')->on('chart_of_accounts');
            $table->string('VatDedAcc', 1)->nullable();
            $table->string('CstmExpAcc', 1)->nullable();
            $table->string('CstmAlcAcc', 1)->nullable();
            $table->string('TaxRegion')->default('PT'); //PT-AC=Azores Islands, PT-MA=Madeira Islands, PT=Continental Portugal
            $table->string('ExemReason', 3)->nullable(); //M01=Article 16th No. 6 of CIVA, M02=Article 6th Law 198/90 June 19th, M03=Cash Liabilities, M04=Exempt Article 13th of CIVA, M05=Exempt Article 14th of CIVA, M06=Exempt Article 15th of CIVA, M07=Exempt Article 9th of CIVA, M08=VAT - Self-Liquidation, M09=VAT - Non-Deductible, M10=VAT - Exempted Company, M11=VAT - Exempted (Tobacco), M12=VA
            $table->string('Agent', 1)->default('N'); //N=No, Y=Yes
            $table->string('OpCode', 1)->nullable();
            $table->string('Export', 1)->default('N'); //N=No, Y=Yes
            $table->string('Section', 1)->nullable();
            $table->string('SplitPaymt', 1)->default('N'); //N=No, Y=Yes
            $table->string('SplitPayAc')->references('id')->on('chart_of_accounts');
            $table->string('TaxAgent', 1)->default('N'); //N=No, Y=Yes
            $table->string('SectionLim', 3)->nullable();
            $table->string('VatSubjCod', 10)->nullable();
            $table->string('VatType', 1)->nullable();
            $table->string('VatCategor', 1)->default('N'); //N=No, Y=Yes
            $table->string('Parag44', 1)->default('N'); //N=No, Y=Yes
            $table->string('ProrataDed', 1)->default('N'); //N=No, Y=Yes
            $table->string('ExcFrmTaxS', 1)->default('N'); //N=No, Y=Yes
            $table->string('CstmActing', 1)->default('N'); //N=No, Y=Yes
            $table->string('CstmActOut')->nullable();
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
        Schema::dropIfExists('o_v_t_g_s');
    }
}
