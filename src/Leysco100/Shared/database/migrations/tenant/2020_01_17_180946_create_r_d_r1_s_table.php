<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRDR1STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_d_r1_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('DocEntry')
                ->references('id')->on('o_r_d_r_s');
            $table->integer('LineNum');
            $table->integer('TargetType')->default(-1);
            $table->string('TrgetEntry', 1)->nullable();
            $table->string('BaseRef', 16)->nullable();
            $table->integer('BaseType')->nullable();
            $table->integer('BaseEntry')->nullable();
            $table->integer('BaseLine')->nullable();
            $table->string('LineStatus', 1)->default('O'); //C=Closed, O=Open
            $table->string('ItemCode', 50)->references('ItemCode')->on('o_i_t_m_s')->nullable();
            $table->integer('ItemID')->references('id')->on('o_i_t_m_s')->nullable();
            $table->string('Dscription', 100)->nullable();
            $table->float('Quantity', 19, 6)->nullable();
            $table->string('ShipDate', 15)->nullable();
            $table->float('OpenQty', 19, 6)->nullable();
            $table->float('Price', 19, 6)->nullable();
            $table->string('Currency', 8)
                ->references('id')->on('currencies')->nullable();
            $table->float('Rate', 19, 6)->nullable();
            $table->float('DiscPrcnt', 19, 6)->nullable();
            $table->float('LineTotal', 19, 6)->nullable();
            $table->float('TotalFrgn', 19, 6)->nullable();
            $table->float('OpenSum', 19, 6)->nullable();
            $table->float('OpenSumFC', 19, 6)->nullable();
            $table->string('VendorNum', 50)->nullable();
            $table->string('SerialNum', 17)->nullable();
            $table->string('WhsCode', 8)
                ->references('id')->on('warehouses')->nullable();
            $table->integer('SlpCode')
                ->references('id')->on('employees')->nullable();
            $table->float('Commission', 19, 6)->nullable();
            $table->string('TreeType', 1)->nullable();
            $table->string('AcctCode', 8)
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('TaxStatus', 1)->nullable();
            $table->float('GrossBuyPr', 19, 6)->nullable();
            $table->float('PriceBefDi', 19, 6)->nullable();
            $table->date('DocDate')->nullable();
            $table->string('Flags', 1)->nullable();
            $table->string('OpenCreQty', 1)->nullable();
            $table->string('UseBaseUn', 1)->default('N');
            $table->string('SubCatNum', 1)->nullable();
            $table->integer('BaseCard')
                ->references('id')->on('o_c_r_d_s')->nullable();
            $table->string('TotalSumSy', 1)->nullable();
            $table->string('OpenSumSys', 1)->nullable();
            $table->string('InvntSttus', 1)->default('O');
            $table->string('OcrCode', 8)->nullable();
            $table->integer('Project')->nullable();
            $table->string('CodeBars', 1)->nullable();
            $table->string('VatPrcnt', 1)->nullable();
            $table->string('VatGroup', 1)->nullable();
            $table->float('PriceAfVAT', 19, 6)->nullable();
            $table->string('Height1', 1)->nullable();
            $table->string('Hght1Unit', 1)->nullable();
            $table->string('Height2', 1)->nullable();
            $table->string('Hght2Unit', 1)->nullable();
            $table->string('Width1', 1)->nullable();
            $table->string('Wdth1Unit', 1)->nullable();
            $table->string('Width2', 1)->nullable();
            $table->string('Wdth2Unit', 1)->nullable();
            $table->string('Length1', 1)->nullable();
            $table->string('Len1Unit', 1)->nullable();
            $table->string('length2', 1)->nullable();
            $table->string('Len2Unit', 1)->nullable();
            $table->string('Volume', 1)->nullable();
            $table->string('VolUnit', 1)->nullable();
            $table->string('Weight1', 1)->nullable();
            $table->string('Wght1Unit', 1)->nullable();
            $table->string('Weight2', 1)->nullable();
            $table->string('Wght2Unit', 1)->nullable();
            $table->string('Factor1', 1)->nullable();
            $table->string('Factor2', 1)->nullable();
            $table->string('Factor3', 1)->nullable();
            $table->string('Factor4', 1)->nullable();
            $table->float('PackQty', 19, 6)->nullable();
            $table->string('UpdInvntry', 1)->nullable();
            $table->integer('BaseDocNum')->nullable();
            $table->string('BaseAtCard', 100)->nullable();
            $table->string('SWW', 1)->nullable();
            $table->float('VatSum', 19, 2)->nullable();
            $table->string('VatSumFrgn', 1)->nullable();
            $table->string('VatSumSy', 1)->nullable();
            $table->integer('FinncPriod')
                ->references('id')->on('o_f_p_r_s')->nullable();
            $table->string('ObjType')->default(17);
            $table->string('LogInstanc', 1)->nullable();
            $table->string('BlockNum', 1)->nullable();
            $table->string('ImportLog', 1)->nullable();
            $table->string('DedVatSum', 1)->nullable();
            $table->string('DedVatSumF', 1)->nullable();
            $table->string('DedVatSumS', 1)->nullable();
            $table->string('IsAqcuistn', 1)->nullable();
            $table->string('DistribSum', 1)->nullable();
            $table->string('DstrbSumFC', 1)->nullable();
            $table->string('DstrbSumSC', 1)->nullable();
            $table->float('GrssProfit', 19, 6)->nullable();
            $table->string('GrssProfSC', 1)->nullable();
            $table->string('GrssProfFC', 1)->nullable();
            $table->string('VisOrder', 1)->nullable();
            $table->string('INMPrice', 1)->nullable();
            $table->integer('PoTrgNum')->nullable();
            $table->string('PoTrgEntry', 1)->nullable();
            $table->string('DropShip', 1)->nullable();
            $table->string('PoLineNum', 1)->nullable();
            $table->string('Address', 254)->nullable();
            $table->string('TaxCode', 8)->nullable();
            $table->string('TaxType', 1)->nullable();
            $table->integer('OrigItem')
                ->references('id')->on('o_i_t_m_s')->nullable();
            $table->string('BackOrdr')->nullable();
            $table->string('FreeTxt', 100)->nullable();
            $table->string('PickStatus', 1)->default('N');
            $table->string('PickOty', 1)->nullable();
            $table->string('PickIdNo', 1)->nullable();
            $table->integer('TrnsCode')
                ->references('id')->on('shipping_types')->nullable();
            $table->string('VatAppld', 1)->nullable();
            $table->string('VatAppldFC', 1)->nullable();
            $table->string('VatAppldSC', 1)->nullable();
            $table->string('BaseQty', 1)->nullable();
            $table->string('BaseOpnQty', 1)->nullable();
            $table->string('VatDscntPr', 1)->nullable();
            $table->string('WtLiable', 1)->nullable();
            $table->string('DeferrTax', 1)->nullable();
            $table->string('EquVatPer', 1)->nullable();
            $table->string('EquVatSum', 1)->nullable();
            $table->string('EquVatSumF', 1)->nullable();
            $table->string('EquVatSumS', 1)->nullable();
            $table->string('LineVat', 1)->nullable();
            $table->string('LineVatlF', 1)->nullable();
            $table->string('LineVatS', 1)->nullable();
            $table->string('unitMsr', 100)->nullable();
            $table->float('NumPerMsr', 19, 6)->nullable();
            $table->string('CEECFlag', 1)->nullable();
            $table->string('ToStock', 1)->nullable();
            $table->string('ToDiff', 1)->nullable();
            $table->string('ExciseAmt', 1)->nullable();
            $table->string('TaxPerUnit', 1)->nullable();
            $table->string('TotInclTax', 1)->nullable();
            $table->string('CountryOrg', 1)->nullable();
            $table->string('StckDstSum', 1)->nullable();
            $table->string('ReleasQtty', 1)->nullable();
            $table->string('LineType', 1)->nullable();
            $table->string('TranType', 1)->nullable();
            $table->string('Text', 16)->nullable();
            $table->integer('OwnerCode')->nullable();
            $table->float('StockPrice', 19, 6)->nullable();
            $table->string('ConsumeFCT', 1)->nullable();
            $table->string('LstByDsSum', 1)->nullable();
            $table->string('StckINMPr', 1)->nullable();
            $table->string('LstBINMPr', 1)->nullable();
            $table->string('StckDstFc', 1)->nullable();
            $table->string('StckDstSc', 1)->nullable();
            $table->string('LstByDsFc', 1)->nullable();
            $table->string('LstByDsSc', 1)->nullable();
            $table->string('StockSum', 1)->nullable();
            $table->string('StockSumFc', 1)->nullable();
            $table->string('StockSumSc', 1)->nullable();
            $table->string('StckSumApp', 1)->nullable();
            $table->string('StckAppFc', 1)->nullable();
            $table->string('StckAppSc', 1)->nullable();
            $table->string('ShipToCode', 1)->nullable();
            $table->string('ShipToDesc', 1)->nullable();
            $table->string('StckAppD', 1)->nullable();
            $table->string('StckAppDFC', 1)->nullable();
            $table->string('StckAppDSC', 1)->nullable();
            $table->string('BasePrice', 1)->nullable();
            $table->float('GTotal', 19, 6)->nullable();
            $table->string('GTotalFC', 1)->nullable();
            $table->string('GTotalSC', 1)->nullable();
            $table->string('DistribExp', 1)->nullable();
            $table->string('DescOW', 1)->nullable();
            $table->string('DetailsOW', 1)->nullable();
            $table->string('GrossBase', 1)->nullable();
            $table->string('VatWoDpm', 1)->nullable();
            $table->string('VatWoDpmFc', 1)->nullable();
            $table->string('VatWoDpmSc', 1)->nullable();
            $table->string('CFOPCode', 1)->nullable();
            $table->string('CSTCode', 1)->nullable();
            $table->string('Usage', 1)->nullable();
            $table->string('TaxOnly', 1)->nullable();
            $table->string('WtCalced', 1)->nullable();
            $table->string('QtyToShip', 1)->nullable();
            $table->float('DelivrdQty', 19, 6)->nullable();
            $table->float('OrderedQty', 19, 6)->nullable();
            $table->string('CogsOcrCod', 8)->nullable()->comment("COGS Distribution Rule Code");
            $table->string('CiOppLineN', 1)->nullable();
            $table->string('CogsAcct', 1)->nullable();
            $table->string('ChgAsmBoMW', 1)->nullable();
            $table->string('ActDelDate', 1)->nullable();
            $table->string('OcrCode2', 8)->nullable()->comment("Costing Center 2");
            $table->string('OcrCode3', 8)->nullable();
            $table->string('OcrCode4', 8)->nullable();
            $table->string('OcrCode5', 8)->nullable();
            $table->string('TaxDistSum', 1)->nullable();
            $table->string('TaxDistSFC', 1)->nullable();
            $table->string('TaxDistSSC', 1)->nullable();
            $table->string('PostTax', 1)->nullable();
            $table->string('Excisable', 1)->nullable();
            $table->string('AssblValue', 1)->nullable();
            $table->string('RG23APart1', 1)->nullable();
            $table->string('RG23APart2', 1)->nullable();
            $table->string('RG23CPart1', 1)->nullable();
            $table->string('RG23CPart2', 1)->nullable();
            $table->string('CogsOcrCo2', 8)->nullable()->comment("COGS Distribution Rule Code2");
            $table->string('CogsOcrCo3', 8)->nullable()->comment("COGS Distribution Rule Code3");
            $table->string('CogsOcrCo4', 8)->nullable()->comment("COGS Distribution Rule Code4");
            $table->string('CogsOcrCo5', 8)->nullable()->comment("COGS Distribution Rule Code5");
            $table->string('LnExcised', 1)->nullable();
            $table->string('LocCode', 1)->nullable();
            $table->string('StockValue', 1)->nullable();
            $table->float('GPTtlBasPr', 19, 6)->nullable();
            $table->string('unitMsr2', 1)->nullable();
            $table->string('NumPerMsr2', 1)->nullable();
            $table->string('SpecPrice', 1)->nullable();
            $table->string('CSTfIPI', 1)->nullable();
            $table->string('CSTfPIS', 1)->nullable();
            $table->string('CSTfCOFINS', 1)->nullable();
            $table->string('ExLineNo', 1)->nullable();
            $table->string('isSrvCall', 1)->nullable();
            $table->float('PQTReqQty')->nullable();
            $table->date('PQTReqDate')->nullable();
            $table->string('PcDocType', 1)->nullable();
            $table->string('PcQuantity', 1)->nullable();
            $table->string('LinManClsd', 1)->nullable();
            $table->string('VatGrpSrc', 1)->nullable();
            $table->string('NoInvtryMv', 1)->nullable();
            $table->string('ActBaseEnt', 1)->nullable();
            $table->string('ActBaseLn', 1)->nullable();
            $table->string('ActBaseNum', 1)->nullable();
            $table->string('OpenRtnQty', 1)->nullable();
            $table->integer('AgrNo')->nullable();
            $table->string('AgrLnNum', 1)->nullable();
            $table->string('CredOrigin', 1)->nullable();
            $table->string('Surpluses', 1)->nullable();
            $table->string('DefBreak', 1)->nullable();
            $table->string('Shortages', 1)->nullable();
            $table->string('UomEntry', 1)->nullable();
            $table->string('UomEntry2', 1)->nullable();
            $table->string('UomCode', 20)->nullable();
            $table->string('UomCode2', 20)->nullable();
            $table->string('FromWhsCod', 8)->nullable();
            $table->string('NeedQty', 1)->nullable();
            $table->string('PartRetire', 1)->nullable();
            $table->string('RetireQty', 1)->nullable();
            $table->string('RetireAPC', 1)->nullable();
            $table->string('RetirAPCFC', 1)->nullable();
            $table->string('RetirAPCSC', 1)->nullable();
            $table->float('InvQty', 19, 6)->nullable();
            $table->float('OpenInvQty', 19, 6)->nullable();
            $table->string('EnSetCost', 1)->nullable();
            $table->string('RetCost', 1)->nullable();
            $table->string('Incoterms', 1)->nullable();
            $table->string('TransMod', 1)->nullable();
            $table->string('LineVendor', 1)->nullable();
            $table->string('DistribIS', 1)->nullable();
            $table->string('ISDistrb', 1)->nullable();
            $table->string('ISDistrbFC', 1)->nullable();
            $table->string('ISDistrbSC', 1)->nullable();
            $table->string('IsByPrdct', 1)->nullable();
            $table->string('ItemType', 1)->nullable();
            $table->string('PriceEdit', 1)->nullable();
            $table->string('PrntLnNum', 1)->nullable();
            $table->string('LinePoPrss', 1)->nullable();
            $table->string('FreeChrgBP', 1)->nullable();
            $table->string('TaxRelev', 1)->nullable();
            $table->string('LegalText', 1)->nullable();
            $table->string('ThirdParty', 1)->nullable();
            $table->string('LicTradNum', 32)->nullable();
            $table->string('InvQtyOnly', 1)->nullable();
            $table->string('UnencReasn', 1)->nullable();
            $table->string('ShipFromCo', 1)->nullable();
            $table->string('ShipFromDe', 1)->nullable();
            $table->string('FisrtBin', 1)->nullable();
            $table->string('AllocBinC', 1)->nullable();
            $table->string('ExpType', 1)->nullable();
            $table->string('ExpUUID', 1)->nullable();
            $table->string('ExpOpType', 1)->nullable();
            $table->string('DIOTNat', 1)->nullable();
            $table->string('MYFtype', 1)->nullable();
            $table->string('GPBefDisc', 1)->nullable();
            $table->string('ReturnRsn', 1)->nullable();
            $table->string('ReturnAct', 1)->nullable();
            $table->string('StgSeqNum', 1)->nullable();
            $table->string('StgEntry', 1)->nullable();
            $table->string('StgDesc', 1)->nullable();
            $table->string('ItmTaxType', 1)->nullable();
            $table->string('SacEntry', 1)->nullable();
            $table->string('NCMCode', 1)->nullable();
            $table->string('HsnEntry', 1)->nullable();
            $table->string('OriBAbsEnt', 1)->nullable();
            $table->string('OriBLinNum', 1)->nullable();
            $table->string('OriBDocTyp', 1)->nullable();
            $table->string('IsPrscGood', 1)->nullable();
            $table->string('IsCstmAct', 1)->nullable();
            $table->string('U_Promotion')->nullable();
            $table->float('QtyAssigned', 19, 6)->default(0);
            $table->string('U_StockWhse', 100)->nullable();
            $table->string('WhsName', 100)->nullable();
            $table->string('BPLId', 100)->nullable();
            $table->string('ExtRef')->nullable()
                ->comment("Used For External Refrence");
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
        Schema::dropIfExists('r_d_r1_s');
    }
}
