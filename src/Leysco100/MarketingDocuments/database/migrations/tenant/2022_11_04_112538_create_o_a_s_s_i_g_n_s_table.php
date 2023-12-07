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
        Schema::create('o_a_s_s_i_g_n_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('DocNum', 100)->nullable();
            $table->string('DocType', 15)->default('I');
            $table->string('CANCELED', 1)->default('N');
            $table->string('Handwrtten', 1)->default('N');
            $table->string('Printed', 1)->default('N');
            $table->string('DocStatus', 1)->default('O');
            $table->string('InvntSttus', 1)->default('O');
            $table->string('Transfered', 1)->default('N');
            $table->string('ObjType')->default(17);
            $table->date('DocDate')->nullable();
            $table->date('DocDueDate')->nullable();
            $table->string('CardID', 50)
                ->references('id')->on('o_c_r_d_s')->nullable();
            $table->string('CardCode', 50)
                ->references('CardCode')->on('o_c_r_d_s')->nullable();
            $table->string('CardName', 200)->nullable();
            $table->string('Address', 254)->nullable();
            $table->string('NumAtCard', 32)->nullable();
            $table->string('VatPercent', 15)->nullable();
            $table->float('VatSum', 19, 2)->nullable();
            $table->string('VatSumFC', 1)->nullable();
            $table->float('DiscPrcnt', 19, 6)->default(0);
            $table->float('DiscSum')->nullable();
            $table->string('DiscSumFC', 1)->nullable();
            $table->string('DocCur')->nullable();
            $table->float('DocRate', 19, 6)->nullable();
            $table->float('DocTotal', 19, 6)->nullable();
            $table->string('DocTotalFC', 1)->nullable();
            $table->string('PaidToDate', 1)->nullable();
            $table->string('PaidFC', 1)->nullable();
            $table->string('GrosProfit', 1)->nullable();
            $table->string('GrosProfFC', 1)->nullable();
            $table->string('Ref1', 11)->nullable();
            $table->string('Ref2', 11)->nullable();
            $table->string('Comments', 100)->nullable();
            $table->string('JrnlMemo', 100)->nullable();
            $table->integer('TransId')->nullable();
            $table->string('ReceiptNum', 1)->nullable();
            $table->integer('GroupNum')->nullable();
            $table->string('DocTime', 1)->nullable();
            $table->integer('SlpCode')->nullable();
            $table->string('TrnspCode', 1)->nullable();
            $table->string('PartSupply', 1)->nullable();
            $table->string('Confirmed', 1)->nullable();
            $table->string('GrossBase', 1)->nullable();
            $table->string('ImportEnt', 1)->nullable();
            $table->string('CreateTran', 1)->nullable();
            $table->string('SummryType', 1)->nullable();
            $table->string('UpdInvnt', 1)->nullable();
            $table->string('UpdCardBal', 1)->nullable();
            $table->string('Instance', 1)->nullable();
            $table->string('Flags', 1)->nullable();
            $table->string('InvntDirec', 1)->nullable();
            $table->string('CntctCode')->nullable();
            $table->string('ShowSCN', 1)->nullable();
            $table->string('FatherCard', 1)->nullable();
            $table->string('SysRate', 19, 6)->nullable();
            $table->string('CurSource', 1)->nullable();
            $table->float('VatSumSy')->nullable();
            $table->float('DiscSumSy')->nullable();
            $table->float('DocTotalSy')->nullable();
            $table->string('PaidSys', 1)->nullable();
            $table->string('FatherType', 1)->nullable();
            $table->string('GrosProfSy', 1)->nullable();
            $table->date('UpdateDate')->nullable();
            $table->string('IsICT', 1)->nullable();
            $table->string('CreateDate', 1)->nullable();
            $table->string('Volume', 1)->nullable();
            $table->string('VolUnit', 1)->nullable();
            $table->string('Weight', 1)->nullable();
            $table->string('WeightUnit', 1)->nullable();
            $table->integer('Series')->nullable();
            $table->date('TaxDate')->nullable();
            $table->string('Filler', 8)->nullable();
            $table->string('DataSource', 1)->nullable();
            $table->string('StampNum', 1)->nullable();
            $table->string('isCrin', 1)->nullable();
            $table->string('FinncPriod', 1)->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('selfInv', 1)->nullable();
            $table->string('VatPaid', 1)->nullable();
            $table->string('VatPaidFC', 1)->nullable();
            $table->string('VatPaidSys', 1)->nullable();
            $table->string('UserSign2', 1)->nullable();
            $table->string('WddStatus', 1)->nullable();
            $table->integer('draftKey')->nullable();
            $table->string('TotalExpns', 1)->nullable();
            $table->string('TotalExpFC', 1)->nullable();
            $table->string('TotalExpSC', 1)->nullable();
            $table->string('DunnLevel', 1)->nullable();
            $table->string('Address2', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('Exported', 1)->nullable();
            $table->string('StationID', 1)->nullable();
            $table->string('Indicator', 1)->nullable();
            $table->string('NetProc', 1)->nullable();
            $table->string('AqcsTax', 1)->nullable();
            $table->string('AqcsTaxFC', 1)->nullable();
            $table->string('AqcsTaxSC', 1)->nullable();
            $table->string('CashDiscPr', 1)->nullable();
            $table->string('CashDiscnt', 1)->nullable();
            $table->string('CashDiscFC', 1)->nullable();
            $table->string('CashDiscSC', 1)->nullable();
            $table->string('ShipToCode', 1)->nullable();
            $table->string('LicTradNum', 32)->nullable();
            $table->string('PaymentRef', 1)->nullable();
            $table->string('WTSum', 1)->nullable();
            $table->string('WTSumFC', 1)->nullable();
            $table->string('WTSumSC', 1)->nullable();
            $table->float('RoundDif', 19, 6)->default(0);
            $table->string('RoundDifFC', 1)->nullable();
            $table->string('RoundDifSy', 1)->nullable();
            $table->string('CheckDigit', 1)->nullable();
            $table->string('Form1099', 1)->nullable();
            $table->string('Box1099', 1)->nullable();
            $table->string('submitted', 1)->nullable();
            $table->string('PoPrss', 1)->nullable();
            $table->string('Rounding', 1)->nullable();
            $table->string('RevisionPo', 1)->nullable();
            $table->string('Segment', 1)->nullable();
            $table->date('ReqDate')->nullable();
            $table->date('CancelDate')->nullable();
            $table->string('PickStatus', 1)->nullable();
            $table->string('Pick', 1)->nullable();
            $table->string('BlockDunn', 1)->nullable();
            $table->string('PeyMethod', 15)->nullable();
            $table->string('PayBlock', 1)->nullable();
            $table->string('PayBlckRef', 1)->nullable();
            $table->string('MaxDscn', 1)->nullable();
            $table->string('Reserve', 1)->nullable();
            $table->string('Max1099', 1)->nullable();
            $table->string('CntrlBnk', 1)->nullable();
            $table->string('PickRmrk', 1)->nullable();
            $table->string('ISRCodLine', 1)->nullable();
            $table->string('ExpAppl', 1)->nullable();
            $table->string('ExpApplFC', 1)->nullable();
            $table->string('ExpApplSC', 1)->nullable();
            $table->string('Project', 1)->nullable();
            $table->string('DeferrTax', 1)->nullable();
            $table->string('LetterNum', 1)->nullable();
            $table->string('FromDate', 1)->nullable();
            $table->string('ToDate', 1)->nullable();
            $table->string('WTApplied', 1)->nullable();
            $table->string('WTAppliedF', 1)->nullable();
            $table->string('BoeReserev', 1)->nullable();
            $table->string('AgentCode', 1)->nullable();
            $table->string('WTAppliedS', 1)->nullable();
            $table->string('EquVatSum', 1)->nullable();
            $table->string('EquVatSumF', 1)->nullable();
            $table->string('EquVatSumS', 1)->nullable();
            $table->string('Installmnt', 1)->nullable();
            $table->string('VATFirst', 1)->nullable();
            $table->string('NnSbAmnt', 1)->nullable();
            $table->string('NnSbAmntSC', 1)->nullable();
            $table->string('NbSbAmntFC', 1)->nullable();
            $table->string('ExepAmnt', 1)->nullable();
            $table->string('ExepAmntSC', 1)->nullable();
            $table->string('ExepAmntFC', 1)->nullable();
            $table->string('VatDate', 1)->nullable();
            $table->string('CorrExt', 1)->nullable();
            $table->string('CorrInv', 1)->nullable();
            $table->string('NCorrInv', 1)->nullable();
            $table->string('CEECFlag', 1)->nullable();
            $table->string('BaseAmnt', 1)->nullable();
            $table->string('BaseAmntSC', 1)->nullable();
            $table->string('BaseAmntFC', 1)->nullable();
            $table->string('CtlAccount', 1)->nullable();
            $table->string('BPLId', 100)->nullable();
            $table->string('BPLName', 100)->nullable();
            $table->string('VATRegNum', 1)->nullable();
            $table->string('TxInvRptNo', 1)->nullable();
            $table->string('TxInvRptDt', 1)->nullable();
            $table->string('KVVATCode', 1)->nullable();
            $table->string('WTDetails', 1)->nullable();
            $table->string('SumAbsId', 1)->nullable();
            $table->string('SumRptDate', 1)->nullable();
            $table->string('PIndicator', 1)->nullable();
            $table->string('ManualNum', 1)->nullable();
            $table->string('UseShpdGd', 3)->nullable();
            $table->string('BaseVtAt', 1)->nullable();
            $table->string('BaseVtAtSC', 1)->nullable();
            $table->string('BaseVtAtFC', 1)->nullable();
            $table->string('NnSbVAt', 1)->nullable();
            $table->string('NnSbVAtSC', 1)->nullable();
            $table->string('NbSbVAtFC', 1)->nullable();
            $table->string('ExptVAt', 1)->nullable();
            $table->string('ExptVAtSC', 1)->nullable();
            $table->string('ExptVAtFC', 1)->nullable();
            $table->string('LYPmtAt', 1)->nullable();
            $table->string('LYPmtAtSC', 1)->nullable();
            $table->string('LYPmtAtFC', 1)->nullable();
            $table->string('ExpAnSum', 1)->nullable();
            $table->string('ExpAnSys', 1)->nullable();
            $table->string('ExpAnFrgn', 1)->nullable();
            $table->string('DocSubType', 1)->nullable();
            $table->string('DpmStatus', 1)->nullable();
            $table->string('DpmAmnt', 1)->nullable();
            $table->string('DpmAmntSC', 1)->nullable();
            $table->string('DpmAmntFC', 1)->nullable();
            $table->string('DpmDrawn', 1)->nullable();
            $table->string('DpmPrcnt', 1)->nullable();
            $table->float('PaidSum', 19, 6)->nullable();
            $table->float('PaidSumFc', 19, 6)->nullable();
            $table->float('PaidSumSc', 19, 6)->nullable();
            $table->string('FolioPref', 1)->nullable();
            $table->string('FolioNum', 1)->nullable();
            $table->string('DpmAppl', 1)->nullable();
            $table->string('DpmApplFc', 1)->nullable();
            $table->string('DpmApplSc', 1)->nullable();
            $table->string('LPgFolioN', 1)->nullable();
            $table->string('Header', 1)->nullable();
            $table->string('Footer', 1)->nullable();
            $table->string('Posted', 1)->nullable();
            $table->string('BPChCode', 1)->nullable();
            $table->string('BPChCntc', 1)->nullable();
            $table->string('PayToCode', 1)->nullable();
            $table->string('IsPaytoBnk', 1)->nullable();
            $table->string('BnkCntry', 1)->nullable();
            $table->string('BankCode', 1)->nullable();
            $table->string('BnkAccount', 1)->nullable();
            $table->string('BnkBranch', 1)->nullable();
            $table->string('isIns', 1)->nullable();
            $table->string('TrackNo', 1)->nullable();
            $table->string('VersionNum', 1)->nullable();
            $table->string('LangCode', 1)->nullable();
            $table->string('BPNameOW', 1)->nullable();
            $table->string('BillToOW', 1)->nullable();
            $table->string('ShipToOW', 1)->nullable();
            $table->string('RetInvoice', 1)->nullable();
            $table->string('ClsDate', 1)->nullable();
            $table->string('MInvNum', 1)->nullable();
            $table->string('MInvDate', 1)->nullable();
            $table->string('SeqCode', 1)->nullable();
            $table->string('Serial', 1)->nullable();
            $table->string('SeriesStr', 1)->nullable();
            $table->string('SubStr', 1)->nullable();
            $table->string('Model', 1)->nullable();
            $table->string('TaxOnExp', 1)->nullable();
            $table->string('TaxOnExpFc', 1)->nullable();
            $table->string('TaxOnExpSc', 1)->nullable();
            $table->string('TaxOnExAp', 1)->nullable();
            $table->string('TaxOnExApF', 1)->nullable();
            $table->string('TaxOnExApS', 1)->nullable();
            $table->string('LastPmnTyp', 1)->nullable();
            $table->string('LndCstNum', 1)->nullable();
            $table->string('UseCorrVat', 1)->nullable();
            $table->string('BlkCredMmo', 1)->nullable();
            $table->string('OpenForLaC', 1)->nullable();
            $table->string('Excised', 1)->nullable();
            $table->string('ExcRefDate', 1)->nullable();
            $table->string('ExcRmvTime', 1)->nullable();
            $table->string('SrvGpPrcnt', 1)->nullable();
            $table->string('DepositNum', 1)->nullable();
            $table->string('CertNum', 1)->nullable();
            $table->string('DutyStatus', 1)->nullable();
            $table->string('AutoCrtFlw', 1)->nullable();
            $table->string('FlwRefDate', 1)->nullable();
            $table->string('FlwRefNum', 1)->nullable();
            $table->string('VatJENum', 1)->nullable();
            $table->string('DpmVat', 1)->nullable();
            $table->string('DpmVatFc', 1)->nullable();
            $table->string('DpmVatSc', 1)->nullable();
            $table->string('DpmAppVat', 1)->nullable();
            $table->string('DpmAppVatF', 1)->nullable();
            $table->string('DpmAppVatS', 1)->nullable();
            $table->string('InsurOp347', 1)->nullable();
            $table->string('IgnRelDoc', 1)->nullable();
            $table->string('BuildDesc', 1)->nullable();
            $table->string('ResidenNum', 1)->nullable();
            $table->string('Checker', 1)->nullable();
            $table->string('Payee', 1)->nullable();
            $table->string('CopyNumber', 1)->nullable();
            $table->string('SSIExmpt', 1)->nullable();
            $table->string('PQTGrpSer', 1)->nullable();
            $table->string('PQTGrpNum', 1)->nullable();
            $table->string('PQTGrpHW', 1)->nullable();
            $table->string('ReopOriDoc', 1)->nullable();
            $table->string('ReopManCls', 1)->nullable();
            $table->string('DocManClsd', 1)->nullable();
            $table->string('ClosingOpt', 1)->nullable();
            $table->string('SpecDate', 1)->nullable();
            $table->string('Ordered', 1)->nullable();
            $table->string('NTSApprov', 1)->nullable();
            $table->string('NTSWebSite', 1)->nullable();
            $table->string('NTSeTaxNo', 1)->nullable();
            $table->string('NTSApprNo', 1)->nullable();
            $table->string('PayDuMonth', 1)->nullable();
            $table->string('ExtraMonth', 1)->nullable();
            $table->string('ExtraDays', 1)->nullable();
            $table->string('CdcOffset', 1)->nullable();
            $table->string('SignMsg', 1)->nullable();
            $table->string('SignDigest', 1)->nullable();
            $table->string('CertifNum', 1)->nullable();
            $table->string('KeyVersion', 1)->nullable();
            $table->string('EDocGenTyp', 1)->nullable();
            $table->string('ESeries', 1)->nullable();
            $table->string('EDocNum', 1)->nullable();
            $table->string('EDocExpFrm', 1)->nullable();
            $table->string('OnlineQuo', 1)->nullable();
            $table->string('POSEqNum', 1)->nullable();
            $table->string('POSManufSN', 1)->nullable();
            $table->string('POSCashN', 1)->nullable();
            $table->string('EDocStatus', 1)->nullable();
            $table->string('EDocCntnt', 1)->nullable();
            $table->string('EDocProces', 1)->nullable();
            $table->string('EDocErrCod', 1)->nullable();
            $table->string('EDocErrMsg', 1)->nullable();
            $table->string('EDocCancel', 1)->nullable();
            $table->string('EDocTest', 1)->nullable();
            $table->string('EDocPrefix', 1)->nullable();
            $table->string('CUP', 1)->nullable();
            $table->string('CIG', 1)->nullable();
            $table->string('DpmAsDscnt', 1)->nullable();
            $table->string('Attachment', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('SupplCode', 1)->nullable();
            $table->string('GTSRlvnt', 1)->nullable();
            $table->string('BaseDisc', 1)->nullable();
            $table->string('BaseDiscSc', 1)->nullable();
            $table->string('BaseDiscFc', 1)->nullable();
            $table->string('BaseDiscPr', 1)->nullable();
            $table->string('CreateTS', 1)->nullable();
            $table->string('UpdateTS', 1)->nullable();
            $table->string('SrvTaxRule', 1)->nullable();
            $table->string('AnnInvDecR', 1)->nullable();
            $table->string('Supplier', 1)->nullable();
            $table->string('Releaser', 1)->nullable();
            $table->string('Receiver', 1)->nullable();
            $table->string('ToWhsCode', 8)->nullable();
            $table->string('AssetDate', 1)->nullable();
            $table->integer('Requester')->nullable();
            $table->string('ReqName', 155)->nullable();
            $table->string('Branch', 1)->nullable();
            $table->integer('Department')->nullable();
            $table->string('Email', 1)->nullable();
            $table->string('Notify', 1)->nullable();
            $table->integer('ReqType')->nullable();
            $table->integer('PQTReqQty')->nullable();
            $table->string('OriginType', 1)->nullable();
            $table->string('IsReuseNum', 1)->nullable();
            $table->string('IsReuseNFN', 1)->nullable();
            $table->string('DocDlvry', 1)->nullable();
            $table->string('PaidDpm', 1)->nullable();
            $table->string('PaidDpmF', 1)->nullable();
            $table->string('PaidDpmS', 1)->nullable();
            $table->string('EnvTypeNFe', 1)->nullable();
            $table->string('AgrNo', 1)->nullable();
            $table->string('IsAlt', 1)->nullable();
            $table->string('AltBaseTyp', 1)->nullable();
            $table->string('AltBaseEnt', 1)->nullable();
            $table->string('AuthCode', 1)->nullable();
            $table->string('StDlvDate', 1)->nullable();
            $table->string('StDlvTime', 1)->nullable();
            $table->string('EndDlvDate', 1)->nullable();
            $table->string('EndDlvTime', 1)->nullable();
            $table->string('VclPlate', 1)->nullable();
            $table->string('ElCoStatus', 1)->nullable();
            $table->string('AtDocType', 1)->nullable();
            $table->string('ElCoMsg', 1)->nullable();
            $table->string('PrintSEPA', 1)->nullable();
            $table->string('FreeChrg', 1)->nullable();
            $table->string('FreeChrgFC', 1)->nullable();
            $table->string('FreeChrgSC', 1)->nullable();
            $table->string('NfeValue', 1)->nullable();
            $table->string('FiscDocNum', 1)->nullable();
            $table->string('RelatedTyp', 1)->nullable();
            $table->string('RelatedEnt', 1)->nullable();
            $table->string('CCDEntry', 1)->nullable();
            $table->string('NfePrntFo', 1)->nullable();
            $table->string('ZrdAbs', 1)->nullable();
            $table->string('POSRcptNo', 1)->nullable();
            $table->string('FoCTax', 1)->nullable();
            $table->string('FoCTaxFC', 1)->nullable();
            $table->string('FoCTaxSC', 1)->nullable();
            $table->string('TpCusPres', 1)->nullable();
            $table->string('ExcDocDate', 1)->nullable();
            $table->string('FoCFrght', 1)->nullable();
            $table->string('FoCFrghtFC', 1)->nullable();
            $table->string('FoCFrghtSC', 1)->nullable();
            $table->string('InterimTyp', 1)->nullable();
            $table->string('PTICode', 1)->nullable();
            $table->string('Letter', 1)->nullable();
            $table->string('FolNumFrom', 1)->nullable();
            $table->string('FolNumTo', 1)->nullable();
            $table->string('FolSeries', 1)->nullable();
            $table->string('SplitTax', 1)->nullable();
            $table->string('SplitTaxFC', 1)->nullable();
            $table->string('SplitTaxSC', 1)->nullable();
            $table->string('ToBinCode', 1)->nullable();
            $table->string('PriceMode', 1)->nullable();
            $table->string('PoDropPrss', 1)->nullable();
            $table->string('PermitNo', 1)->nullable();
            $table->string('MYFtype', 1)->nullable();
            $table->string('DocTaxID', 1)->nullable();
            $table->string('DateReport', 1)->nullable();
            $table->string('RepSection', 1)->nullable();
            $table->string('ExclTaxRep', 1)->nullable();
            $table->string('PosCashReg', 1)->nullable();
            $table->string('DmpTransID', 1)->nullable();
            $table->string('ECommerBP', 1)->nullable();
            $table->string('EComerGSTN', 1)->nullable();
            $table->string('Revision', 1)->nullable();
            $table->string('RevRefNo', 1)->nullable();
            $table->string('RevRefDate', 1)->nullable();
            $table->string('RevCreRefN', 1)->nullable();
            $table->string('RevCreRefD', 1)->nullable();
            $table->string('TaxInvNo', 1)->nullable();
            $table->string('FrmBpDate', 1)->nullable();
            $table->string('GSTTranTyp', 1)->nullable();
            $table->integer('BaseType')->nullable();
            $table->integer('BaseEntry')->nullable();
            $table->string('ComTrade', 1)->nullable();
            $table->string('UseBilAddr', 1)->nullable();
            $table->string('IssReason', 1)->nullable();
            $table->string('ComTradeRt', 1)->nullable();
            $table->string('SplitPmnt', 1)->nullable();
            $table->string('SOIWizId', 1)->nullable();
            $table->string('SelfPosted', 1)->nullable();
            $table->string('EnBnkAcct', 1)->nullable();
            $table->string('EncryptIV', 1)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->string('EWBGenType', 1)->nullable();
            $table->string('SAPPassprt', 1)->nullable();
            $table->string('CtActTax', 1)->nullable();
            $table->string('CtActTaxFC', 1)->nullable();
            $table->string('CtActTaxSC', 1)->nullable();
            $table->string('U_SaleType')->nullable();
            $table->string('U_CashName')->nullable();
            $table->string('U_CashNo')->nullable();
            $table->string('U_IDNo')->nullable();
            $table->integer('OwnerCode')->nullable(); //Document Owner
            $table->string('U_ServiceCall')->nullable(); //Service Call
            $table->string('U_BaseDoc')->nullable(); // Base Document Number
            $table->string('U_SalePipe')->nullable(); // Sale Pipe Line
            $table->string('U_DemoLocation')->nullable(); //Demo Location
            $table->string('U_Technician')->nullable(); // Technician
            $table->string('U_Location')->nullable(); //Location
            $table->string('U_MpesaRefNo')->nullable(); //Mpesa Ref No
            $table->string('U_PCash')->nullable();
            $table->string('U_SSerialNo')->nullable();
            $table->string('U_transferType')->nullable();
            $table->string('U_TypePur')->nullable();
            $table->string('U_Staff')->nullable();
            $table->string('U_NegativeMargin')->nullable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
            $table->float('ExtDocTotal', 19, 2)->default(0)->comment("Used For External Doc Total");
            $table->integer('ClgCode')->nullable();
            $table->integer('AssEmp')->nullable();
            $table->string('NumAtCard2', 100)
                ->references('id')->on('o_d_r_d_s')->nullable();
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
        Schema::dropIfExists('o_a_s_s_i_g_n_s');
    }
};
