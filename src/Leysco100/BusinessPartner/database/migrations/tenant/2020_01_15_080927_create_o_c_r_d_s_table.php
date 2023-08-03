<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCRDSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_r_d_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CardCode', 100);
            $table->string('CardName', 100)->nullable();
            $table->string('CardType', 1)->default('C');
            $table->string('GroupCode')
                ->references('id')->on('o_c_r_g_s')->nullable();
            $table->integer('ChannCode')
                ->references('id')->on('channels')->nullable();
            $table->integer('TierCode')
                ->references('id')->on('tiers')->nullable();
            $table->string('CmpPrivate')->nullable();
            $table->string('Distributor')->nullable();
            $table->string('Longitude', 50)->nullable();
            $table->string('Latitude', 50)->nullable();
            $table->string('Address', 200)->nullable();
            $table->string('ZipCode', 19)->nullable();
            $table->string('MailAddres', 19)->nullable();
            $table->string('MailZipCod', 19)->nullable();
            $table->string('Phone1', 19)->nullable();
            $table->string('Phone2', 19)->nullable();
            $table->string('Fax', 19)->nullable();
            $table->string('CntctPrsn', 100)->nullable();
            $table->string('Notes', 19)->nullable();
            $table->float('Balance', 19, 6)->nullable();
            $table->float('ChecksBal', 19, 6)->nullable();
            $table->float('DNotesBal', 19, 6)->nullable();
            $table->float('OrdersBal', 19, 6)->nullable();
            $table->integer('GroupNum')->nullable();
            $table->float('CreditLine', 19, 6)->nullable();
            $table->float('DebtLine', 19, 6)->nullable();
            $table->float('Discount', 19, 6)->nullable();
            $table->string('VatStatus', 1)->default('Y');
            $table->string('LicTradNum', 32)->nullable();
            $table->string('DdctStatus', 1)->default('N');
            $table->float('DdctPrcnt', 19, 6)->nullable();
            $table->string('ValidUntil')->nullable();
            $table->integer('Chrctrstcs')->nullable();
            $table->integer('ExMatchNum')->nullable();
            $table->integer('InMatchNum')->nullable();
            $table->integer('ListNum')
                ->references('id')->on('o_p_l_n_s')->default(1);
            $table->float('DNoteBalFC', 19, 6)->nullable();
            $table->float('OrderBalFC', 19, 6)->nullable();
            $table->float('DNoteBalSy', 19, 6)->nullable();
            $table->float('OrderBalSy', 19, 6)->nullable();
            $table->float('Transfered', 19, 6)->nullable();
            $table->float('BalTrnsfrd', 19, 6)->nullable();
            $table->string('IntrstRate')->nullable();
            $table->float('Commission', 19, 6)->nullable();
            $table->integer('CommGrCode')
                ->references('id')->on('commission_groups')->nullable();
            $table->string('Free_Text', 16)->nullable();
            $table->string('SlpCode')->nullable();
            $table->string('PrevYearAc', 1)->default('N');
            $table->string('Currency')
                ->references('id')->on('currencies')->nullable();
            $table->string('RateDifAct')->nullable();
            $table->float('BalanceSys', 19, 6)->nullable();
            $table->float('BalanceFC', 19, 6)->nullable();
            $table->string('Protected', 1)->default('N');
            $table->string('Cellular', 20)->nullable();
            $table->string('AvrageLate', 1)->nullable();
            $table->string('City', 1)->nullable();
            $table->string('County', 1)->nullable();
            $table->string('Country', 1)->nullable();
            $table->string('MailCity', 1)->nullable();
            $table->string('MailCounty', 1)->nullable();
            $table->string('MailCountr', 1)->nullable();
            $table->string('E_Mail', 25)->nullable();
            $table->string('Picture', 1)->nullable();
            $table->string('DflAccount', 1)->nullable();
            $table->string('DflBranch', 1)->nullable();
            $table->string('BankCode', 1)->nullable();
            $table->string('AddID', 64)->nullable();
            $table->string('Pager', 1)->nullable();
            $table->string('FatherCard', 1)->nullable();
            $table->string('CardFName', 100)->nullable();
            $table->string('FatherType', 1)->nullable();
            $table->string('DdctOffice', 1)->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('ExportCode', 1)->nullable();
            $table->string('DscntObjct', 1)->nullable();
            $table->string('DscntRel', 1)->nullable();
            $table->string('SPGCounter', 1)->nullable();
            $table->string('SPPCounter', 1)->nullable();
            $table->string('DdctFileNo', 1)->nullable();
            $table->string('SCNCounter', 1)->nullable();
            $table->string('MinIntrst', 1)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('OprCount', 1)->nullable();
            $table->string('ExemptNo', 1)->nullable();
            $table->string('Priority', 1)->nullable();
            $table->string('CreditCard', 1)->nullable();
            $table->string('CrCardNum', 1)->nullable();
            $table->string('CardValid', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('LocMth', 1)->nullable();
            $table->string('validFor', 1)->nullable();
            $table->string('validFrom', 1)->nullable();
            $table->string('validTo', 1)->nullable();
            $table->string('frozenFor', 1)->default("N");
            $table->string('frozenFrom', 1)->nullable();
            $table->string('frozenTo', 1)->nullable();
            $table->string('sEmployed', 1)->nullable();
            $table->string('MTHCounter', 1)->nullable();
            $table->string('BNKCounter', 1)->nullable();
            $table->string('DdgKey', 1)->nullable();
            $table->string('DdtKey', 1)->nullable();
            $table->string('ValidComm', 1)->nullable();
            $table->string('FrozenComm', 1)->nullable();
            $table->string('chainStore', 1)->nullable();
            $table->string('DiscInRet', 1)->nullable();
            $table->string('State1', 1)->nullable();
            $table->string('State2', 1)->nullable();
            $table->string('VatGroup', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('Indicator', 1)->nullable();
            $table->string('ShipType', 1)->nullable();
            $table->integer('DebPayAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('ShipToDef', 1)->nullable();
            $table->string('Block', 1)->nullable();
            $table->string('MailBlock', 1)->nullable();
            $table->string('Password', 1)->nullable();
            $table->string('ECVatGroup', 1)->nullable();
            $table->string('Deleted', 1)->nullable();
            $table->string('IBAN', 1)->nullable();
            $table->string('DocEntry', 1)->nullable();
            $table->string('FormCode', 1)->nullable();
            $table->string('Box1099', 1)->nullable();
            $table->string('PymCode', 1)->nullable();
            $table->string('BackOrder', 1)->nullable();
            $table->string('PartDelivr', 1)->nullable();
            $table->string('DunnLevel', 1)->nullable();
            $table->string('DunnDate', 1)->nullable();
            $table->string('BlockDunn', 1)->nullable();
            $table->string('BankCountr', 1)->nullable();
            $table->string('CollecAuth', 1)->nullable();
            $table->string('DME', 1)->nullable();
            $table->string('InstrucKey', 1)->nullable();
            $table->string('SinglePaym', 1)->nullable();
            $table->string('ISRBillId', 1)->nullable();
            $table->string('PaymBlock', 1)->nullable();
            $table->string('RefDetails', 1)->nullable();
            $table->string('HouseBank', 1)->nullable();
            $table->string('OwnerIdNum', 1)->nullable();
            $table->string('PyBlckDesc', 1)->nullable();
            $table->string('HousBnkCry', 1)->nullable();
            $table->string('HousBnkAct', 1)->nullable();
            $table->string('HousBnkBrn', 1)->nullable();
            $table->string('ProjectCod', 1)->nullable();
            $table->string('SysMatchNo', 1)->nullable();
            $table->string('VatIdUnCmp', 1)->nullable();
            $table->string('AgentCode', 1)->nullable();
            $table->string('TolrncDays', 1)->nullable();
            $table->string('SelfInvoic', 1)->nullable();
            $table->string('DeferrTax', 1)->nullable();
            $table->string('LetterNum', 1)->nullable();
            $table->string('MaxAmount', 1)->nullable();
            $table->string('FromDate', 1)->nullable();
            $table->string('ToDate', 1)->nullable();
            $table->string('WTLiable', 1)->nullable();
            $table->string('CrtfcateNO', 1)->nullable();
            $table->string('ExpireDate', 1)->nullable();
            $table->string('NINum', 1)->nullable();
            $table->string('AccCritria', 1)->nullable();
            $table->string('WTCode', 1)->nullable();
            $table->string('Equ', 1)->nullable();
            $table->string('HldCode', 1)->nullable();
            $table->string('ConnBP', 1)->nullable();
            $table->string('MltMthNum', 1)->nullable();
            $table->string('TypWTReprt', 1)->nullable();
            $table->string('VATRegNum', 1)->nullable();
            $table->string('RepName', 1)->nullable();
            $table->string('Industry', 1)->nullable();
            $table->string('Business', 1)->nullable();
            $table->string('WTTaxCat', 1)->nullable();
            $table->string('IsDomestic', 1)->nullable();
            $table->string('IsResident', 1)->nullable();
            $table->string('AutoCalBCG', 1)->nullable();
            $table->string('OtrCtlAcct', 1)->nullable();
            $table->string('AliasName', 1)->nullable();
            $table->string('Building', 1)->nullable();
            $table->string('MailBuildi', 1)->nullable();
            $table->string('BoEPrsnt', 1)->nullable();
            $table->string('BoEDiscnt', 1)->nullable();
            $table->string('BoEOnClct', 1)->nullable();
            $table->string('UnpaidBoE', 1)->nullable();
            $table->string('ITWTCode', 1)->nullable();
            $table->string('DunTerm', 1)->nullable();
            $table->string('ChannlBP', 1)->nullable();
            $table->string('DfTcnician', 1)->nullable();
            $table->integer('Territory')
                ->references('id')->on('o_t_e_r_s')->nullable();
            $table->string('BillToDef', 1)->nullable();
            $table->integer('DpmClear')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('IntrntSite', 1)->nullable();
            $table->string('LangCode', 1)->nullable();
            $table->string('HousActKey', 1)->nullable();
            $table->string('Profession', 1)->nullable();
            $table->string('CDPNum', 1)->nullable();
            $table->string('DflBankKey', 1)->nullable();
            $table->string('BCACode', 1)->nullable();
            $table->string('UseShpdGd', 1)->nullable();
            $table->string('RegNum', 1)->nullable();
            $table->string('VerifNum', 1)->nullable();
            $table->string('BankCtlKey', 1)->nullable();
            $table->string('HousCtlKey', 1)->nullable();
            $table->string('AddrType', 1)->nullable();
            $table->string('InsurOp347', 1)->nullable();
            $table->string('MailAddrTy', 1)->nullable();
            $table->string('StreetNo', 1)->nullable();
            $table->string('MailStrNo', 1)->nullable();
            $table->string('TaxRndRule', 1)->nullable();
            $table->string('VendTID', 1)->nullable();
            $table->string('ThreshOver', 1)->nullable();
            $table->string('SurOver', 1)->nullable();
            $table->string('VendorOcup', 1)->nullable();
            $table->string('OpCode347', 1)->nullable();
            $table->integer('DpmIntAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('ResidenNum', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->string('PlngGroup', 1)->nullable();
            $table->string('VatIDNum', 1)->nullable();
            $table->string('Affiliate', 1)->nullable();
            $table->string('MivzExpSts', 1)->nullable();
            $table->string('HierchDdct', 1)->nullable();
            $table->string('CertWHT', 1)->nullable();
            $table->string('CertBKeep', 1)->nullable();
            $table->string('WHShaamGrp', 1)->nullable();
            $table->string('IndustryC', 1)->nullable();
            $table->string('DatevAcct', 1)->nullable();
            $table->string('DatevFirst', 1)->nullable();
            $table->string('GTSRegNum', 1)->nullable();
            $table->string('GTSBankAct', 1)->nullable();
            $table->string('GTSBilAddr', 1)->nullable();
            $table->string('HsBnkSwift', 1)->nullable();
            $table->string('HsBnkIBAN', 1)->nullable();
            $table->string('DflSwift', 1)->nullable();
            $table->string('AutoPost', 1)->nullable();
            $table->string('IntrAcc', 1)->nullable();
            $table->string('FeeAcc', 1)->nullable();
            $table->string('CpnNo', 1)->nullable();
            $table->string('NTSWebSite', 1)->nullable();
            $table->string('DflIBAN', 1)->nullable();
            $table->integer('Series')->nullable();
            $table->string('Number', 1)->nullable();
            $table->string('EDocExpFrm', 1)->nullable();
            $table->string('TaxIdIdent', 1)->nullable();
            $table->string('Attachment', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('DiscRel', 1)->nullable();
            $table->string('NoDiscount', 1)->nullable();
            $table->string('SCAdjust', 1)->nullable();
            $table->string('DflAgrmnt', 1)->nullable();
            $table->string('GlblLocNum', 1)->nullable();
            $table->string('SenderID', 1)->nullable();
            $table->string('RcpntID', 1)->nullable();
            $table->string('MainUsage', 1)->nullable();
            $table->string('SefazCheck', 1)->nullable();
            $table->string('free312', 1)->nullable();
            $table->string('free313', 1)->nullable();
            $table->string('DateFrom', 1)->nullable();
            $table->string('DateTill', 1)->nullable();
            $table->string('RelCode', 1)->nullable();
            $table->string('OKATO', 1)->nullable();
            $table->string('OKTMO', 1)->nullable();
            $table->string('KBKCode', 1)->nullable();
            $table->string('TypeOfOp', 1)->nullable();
            $table->integer('OwnerCode')->nullable();
            $table->string('MandateID', 1)->nullable();
            $table->string('SignDate', 1)->nullable();
            $table->string('Remark1', 1)->nullable();
            $table->string('ConCerti', 1)->nullable();
            $table->string('TpCusPres', 1)->nullable();
            $table->string('RoleTypCod', 1)->nullable();
            $table->string('BlockComm', 1)->nullable();
            $table->string('EmplymntCt', 1)->nullable();
            $table->string('ExcptnlEvt', 1)->nullable();
            $table->string('ExpnPrfFnd', 1)->nullable();
            $table->string('EdrsFromBP', 1)->nullable();
            $table->string('EdrsToBP', 1)->nullable();
            $table->string('CreateTS', 1)->nullable();
            $table->string('UpdateTS', 1)->nullable();
            $table->string('EDocGenTyp', 1)->nullable();
            $table->string('eStreet', 1)->nullable();
            $table->string('eStreetNum', 1)->nullable();
            $table->string('eBuildnNum', 1)->nullable();
            $table->string('eZipCode', 1)->nullable();
            $table->string('eCityTown', 1)->nullable();
            $table->string('eCountry', 1)->nullable();
            $table->string('eDistrict', 1)->nullable();
            $table->string('RepFName', 1)->nullable();
            $table->string('RepSName', 1)->nullable();
            $table->string('RepCmpName', 1)->nullable();
            $table->string('RepFisCode', 1)->nullable();
            $table->string('RepAddID', 1)->nullable();
            $table->string('PECAddr', 1)->nullable();
            $table->string('IPACodePA', 1)->nullable();
            $table->string('PriceMode', 1)->nullable();
            $table->string('EffecPrice', 1)->nullable();
            $table->string('TxExMxVdTp', 1)->nullable();
            $table->string('MerchantID', 1)->nullable();
            $table->string('UseBilAddr', 1)->nullable();
            $table->string('NaturalPer', 1)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->string('EnAddID', 1)->nullable();
            $table->string('EncryptIV', 1)->nullable();
            $table->string('EnDflAccnt', 1)->nullable();
            $table->string('EnDflIBAN', 1)->nullable();
            $table->string('EnERD4In', 1)->nullable();
            $table->string('EnERD4Out', 1)->nullable();
            $table->string('DflCustomr', 1)->nullable();
            $table->string('TspEntry', 1)->nullable();
            $table->string('TspLine', 1)->nullable();
            $table->string('ExtRef')->nullable(); //Name
            $table->integer('isBlocked')->default(0)->comment("Blocked from being sold to, 0=No, 1=Yes");
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
        Schema::dropIfExists('o_c_r_d_s');
    }
}
