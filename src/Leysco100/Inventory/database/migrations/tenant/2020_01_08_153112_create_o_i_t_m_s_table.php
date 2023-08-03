<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOITMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_i_t_m_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ItemCode', 50)->nullable();
            $table->string('ItemName', 100)->nullable();
            $table->string('FrgnName')->nullable();
            $table->integer('ItmsGrpCod')
                ->references('id')->on('o_i_t_b_s')->nullable();
            $table->string('CstGrpCode')->nullable();
            $table->string('VatGourpSa')
                ->references('id')->on('tax_groups')->nullable();
            $table->string('CodeBars', 254)->nullable();
            ;
            $table->string('VATLiable', 1)->default('Y');
            $table->string('PrchseItem', 1)->default('Y');
            $table->string('SellItem')->default('Y');
            $table->string('InvntItem')->default('Y');
            $table->float('OnHand', 19, 6)->default(0);
            $table->float('IsCommited', 19, 6)->default(0);
            $table->float('OnOrder', 19, 6)->default(0);
            $table->integer('IncomeAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExmptIncom')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('MaxLevel')->default(0);
            $table->string('DfltWH')->nullable();
            $table->string('CardCode')
                ->references('id')->on('business_partners')->nullable();
            $table->string('SuppCatNum')->nullable();
            $table->string('BuyUnitMsr')->nullable();
            $table->integer('NumInBuy')->default(1);
            $table->integer('ReorderQty')->default(0);
            $table->string('MinLevel')->nullable();
            $table->decimal('LstEvlPric', 19, 6)->nullable();
            $table->string('LstEvlDate')->nullable();
            $table->string('CustomPer', 1)->nullable();
            $table->string('Canceled', 1)->default('N');
            $table->string('MnufctTime', 1)->nullable();
            $table->string('WholSlsTax', 1)->nullable();
            $table->string('RetilrTax', 1)->nullable();
            $table->string('SpcialDisc', 1)->nullable();
            $table->string('DscountCod', 1)->nullable();
            $table->string('TrackSales', 1)->default('N');
            $table->string('SalUnitMsr', 50)->nullable();
            $table->float('NumInSale', 19, 6)->nullable();
            $table->string('Consig', 1)->nullable();
            $table->integer('QueryGroup')->default(0);
            $table->string('Counted', 1)->nullable();
            $table->string('OpenBlnc', 1)->nullable();
            $table->string('EvalSystem', 1)->nullable();
            $table->string('UserSign')
                ->references('id')->on('users')->nullable();
            $table->string('FREE', 1)->default('N');
            $table->string('PicturName', 1)->nullable();
            $table->string('Transfered', 1)->default('N');
            $table->string('BlncTrnsfr', 1)->nullable();
            $table->string('UserText', 1)->nullable();
            $table->string('SerialNum', 17)->nullable();
            $table->string('CommisPcnt', 1)->nullable();
            $table->string('CommisSum', 1)->nullable();
            $table->string('CommisGrp')
                ->references('id')->on('commission_groups')->nullable();
            $table->string('TreeType', 1)->nullable();
            $table->string('TreeQty', 1)->nullable();
            $table->string('LastPurPrc', 1)->nullable();
            $table->string('LastPurCur', 1)->nullable();
            $table->string('LastPurDat', 1)->nullable();
            $table->string('ExitCur', 1)->nullable();
            $table->string('ExitPrice', 1)->nullable();
            $table->string('ExitWH', 1)->nullable();
            $table->string('AssetItem', 1)->default('N');
            $table->string('WasCounted', 1)->default('N');
            $table->string('ManSerNum', 1)->default('N');
            $table->string('SHeight1', 1)->nullable();
            $table->string('SHght1Unit', 1)->nullable();
            $table->string('SHeight2', 1)->nullable();
            $table->string('SHght2Unit', 1)->nullable();
            $table->string('SWidth1', 1)->nullable();
            $table->string('SWdth1Unit', 1)->nullable();
            $table->string('SWidth2', 1)->nullable();
            $table->string('SWdth2Unit', 1)->nullable();
            $table->string('SLength1', 1)->nullable();
            $table->string('SLen1Unit', 1)->nullable();
            $table->string('Slength2', 1)->nullable();
            $table->string('SLen2Unit', 1)->nullable();
            $table->float('SVolume', 19, 6)->nullable();
            $table->string('SVolUnit', 1)->nullable();
            $table->float('SWeight1', 19, 6)->nullable();
            $table->integer('SWght1Unit')->nullable();
            $table->float('SWeight2', 19, 6)->nullable();
            $table->integer('SWght2Unit')->nullable();
            $table->string('BHeight1', 1)->nullable();
            $table->string('BHght1Unit', 1)->nullable();
            $table->string('BHeight2', 1)->nullable();
            $table->string('BHght2Unit', 1)->nullable();
            $table->string('BWidth1', 1)->nullable();
            $table->string('BWdth1Unit', 1)->nullable();
            $table->string('BWidth2', 1)->nullable();
            $table->string('BWdth2Unit', 1)->nullable();
            $table->string('BLength1', 1)->nullable();
            $table->string('BLen1Unit', 1)->nullable();
            $table->string('Blength2', 1)->nullable();
            $table->string('BLen2Unit', 1)->nullable();
            $table->string('BVolume', 1)->nullable();
            $table->string('BVolUnit', 1)->nullable();
            $table->string('BWeight1', 1)->nullable();
            $table->string('BWght1Unit', 1)->nullable();
            $table->string('BWeight2', 1)->nullable();
            $table->string('BWght2Unit', 1)->nullable();
            $table->string('FixCurrCms', 1)->nullable();
            $table->string('FirmCode')
                ->references('id')->on('manufactures')->nullable();
            $table->string('LstSalDate', 1)->nullable();
            $table->string('ExportCode', 1)->nullable();
            $table->string('SalFactor1', 1)->nullable();
            $table->string('SalFactor2', 1)->nullable();
            $table->string('SalFactor3', 1)->nullable();
            $table->string('SalFactor4', 1)->nullable();
            $table->string('PurFactor1', 1)->nullable();
            $table->string('PurFactor2', 1)->nullable();
            $table->string('PurFactor3', 1)->nullable();
            $table->string('PurFactor4', 1)->nullable();
            $table->string('SalFormula', 1)->nullable();
            $table->string('PurFormula', 1)->nullable();
            $table->string('VatGroupPu')->nullable();
            $table->float('AvgPrice', 19, 3)->nullable();
            $table->string('PurPackMsr', 1)->nullable();
            $table->string('PurPackUn', 1)->nullable();
            $table->string('SalPackMsr', 100)->nullable();
            $table->float('SalPackUn', 19, 6)->nullable();
            $table->string('SCNCounter', 1)->default();
            $table->string('ManBtchNum', 1)->default('N');
            $table->string('ManOutOnly', 1)->default('N');
            $table->string('DataSource', 1)->default('N');
            $table->string('validFor', 1)->default('N');
            $table->date('validFrom')->nullable();
            $table->date('validTo')->nullable();
            $table->string('frozenFor', 1)->default('N');
            $table->string('frozenFrom', 1)->nullable();
            $table->string('frozenTo', 1)->nullable();
            $table->string('BlockOut', 1)->default('N');
            $table->string('ValidComm', 1)->nullable();
            $table->string('FrozenComm', 1)->nullable();
            $table->string('LogInstanc', 1)->nullable();
            $table->string('ObjType', 1)->nullable();
            $table->string('SWW', 1)->nullable();
            $table->string('Deleted', 1)->default('N');
            $table->string('DocEntry', 1)->nullable();
            $table->string('ExpensAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('FrgnInAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('ShipType')
                ->references('id')->on('shipping_types')->nullable();
            $table->string('GLMethod')->default('W');
            $table->string('ECInAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('FrgnExpAcc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('ECExpAcc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->string('TaxType', 1)->default('Y');
            $table->string('ByWh', 1)->nullable();
            $table->string('WTLiable', 1)->default('Y');
            $table->string('ItemType', 1)->default('I');
            $table->string('WarrntTmpl', 1)->nullable();
            $table->string('BaseUnit', 1)->nullable();
            $table->string('CountryOrg', 1)->nullable();
            $table->string('StockValue', 1)->nullable();
            $table->string('Phantom', 1)->nullable();
            $table->string('IssueMthd', 1)->nullable();
            $table->string('FREE1', 1)->nullable();
            $table->string('PricingPrc', 1)->nullable();
            $table->string('MngMethod', 1)->nullable();
            $table->string('ReorderPnt', 1)->nullable();
            $table->string('InvntryUom')->nullable();
            $table->string('PlaningSys', 1)->nullable();
            $table->string('PrcrmntMtd', 1)->nullable();
            $table->string('OrdrIntrvl', 1)->nullable();
            $table->string('OrdrMulti', 1)->nullable();
            $table->string('MinOrdrQty', 1)->nullable();
            $table->string('LeadTime', 1)->nullable();
            $table->string('IndirctTax', 1)->nullable();
            $table->string('TaxCodeAR', 1)->nullable();
            $table->string('TaxCodeAP', 1)->nullable();
            $table->string('OSvcCode', 1)->nullable();
            $table->string('ISvcCode', 1)->nullable();
            $table->string('ServiceGrp', 1)->nullable();
            $table->string('NCMCode', 1)->nullable();
            $table->string('MatType', 1)->nullable();
            $table->string('MatGrp', 1)->nullable();
            $table->string('ProductSrc', 1)->nullable();
            $table->string('ServiceCtg', 1)->nullable();
            $table->string('ItemClass', 1)->nullable();
            $table->string('Excisable', 1)->nullable();
            $table->string('ChapterID', 1)->nullable();
            $table->string('NotifyASN', 1)->nullable();
            $table->string('ProAssNum', 1)->nullable();
            $table->string('AssblValue', 1)->nullable();
            $table->string('DNFEntry', 1)->nullable();
            $table->string('UserSign2')
                ->references('id')->on('users')->nullable();
            $table->string('Spec', 1)->nullable();
            $table->string('TaxCtg', 1)->nullable();
            $table->integer('Series')->nullable();
            $table->string('Number', 1)->nullable();
            $table->string('FuelCode', 1)->nullable();
            $table->string('BeverTblC', 1)->nullable();
            $table->string('BeverGrpC', 1)->nullable();
            $table->string('BeverTM', 1)->nullable();
            $table->string('Attachment', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('ToleranDay', 1)->nullable();
            $table->integer('UgpEntry')
                ->references('id')->on('o_u_g_p_s')->nullable();
            $table->integer('PUoMEntry')
                ->references('id')->on('uo_m_s')->nullable();
            $table->integer('SUoMEntry')
                ->references('id')->on('uo_m_s')->nullable();
            $table->integer('IUoMEntry')
                ->references('id')->on('uo_m_s')->nullable();
            $table->string('IssuePriBy', 1)->nullable();
            $table->string('AssetClass', 1)->nullable();
            $table->string('AssetGroup', 1)->nullable();
            $table->string('InventryNo', 1)->nullable();
            $table->string('Technician', 1)->nullable();
            $table->string('Employee', 1)->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('StatAsset', 1)->default('N');
            $table->string('Cession', 1)->default('N');
            $table->string('DeacAftUL', 1)->default('N');
            $table->string('AsstStatus', 1)->default('N');
            $table->string('CapDate', 1)->nullable();
            $table->string('AcqDate', 1)->nullable();
            $table->string('RetDate', 1)->nullable();
            $table->string('GLPickMeth', 1)->default('A');
            $table->string('NoDiscount', 1)->default('N');
            $table->string('MgrByQty', 1)->default('N');
            $table->string('AssetRmk1', 1)->nullable();
            $table->string('AssetRmk2', 1)->nullable();
            $table->string('AssetAmnt1', 1)->nullable();
            $table->string('AssetAmnt2', 1)->nullable();
            $table->string('DeprGroup', 1)->nullable();
            $table->string('AssetSerNo', 1)->nullable();
            $table->string('CntUnitMsr', 100)->nullable();
            $table->float('NumInCnt', 19, 6)->nullable();
            $table->string('INUoMEntry')->nullable();
            $table->string('OneBOneRec', 1)->nullable();
            $table->string('RuleCode', 1)->nullable();
            $table->string('ScsCode', 1)->nullable();
            $table->string('SpProdType', 1)->nullable();
            $table->string('IWeight1', 1)->nullable();
            $table->string('IWght1Unit', 1)->nullable();
            $table->string('IWeight2', 1)->nullable();
            $table->string('IWght2Unit', 1)->nullable();
            $table->string('CompoWH', 1)->default('B');
            $table->string('CreateTS', 1)->nullable();
            $table->string('UpdateTS', 1)->nullable();
            $table->string('VirtAstItm', 1)->nullable();
            $table->string('SouVirAsst', 1)
                ->references('id')->on('o_i_t_m_s')->nullable();
            $table->string('InCostRoll', 1)->default('Y');
            $table->string('PrdStdCst', 1)->nullable();
            $table->string('EnAstSeri', 1)->nullable();
            $table->string('LinkRsc', 1)->nullable();
            $table->string('OnHldPert', 1)->nullable();
            $table->string('onHldLimt', 1)->nullable();
            $table->integer('PriceUnit')
                ->references('id')->on('uo_m_s')->nullable();
            $table->string('GSTRelevnt', 1)->nullable();
            $table->string('SACEntry', 1)->nullable();
            $table->string('GstTaxCtg', 1)->nullable();
            $table->string('AssVal4WTR', 1)->nullable();
            $table->string('ExcImpQUoM', 1)->nullable();
            $table->string('ExcFixAmnt', 1)->nullable();
            $table->string('ExcRate', 1)->nullable();
            $table->string('SOIExc', 1)->nullable();
            $table->string('TNVED', 1)->nullable();
            $table->string('Imported', 1)->default('N');
            $table->string('AutoBatch', 1)->default('N');
            $table->string('CstmActing', 1)->default('N');

            //Accounts Added By Kibet
            $table->integer('BalInvntAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('SaleCostAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('TransferAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('RevenuesAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('VarianceAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('DecreasAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('IncreasAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ReturnAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpensesAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('EURevenuAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('EUExpensAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FrRevenuAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FrExpensAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PriceDifAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExchangeAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('BalanceAcc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PurchaseAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PAReturnAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('PurchOfsAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('FedTaxID')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('Building')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ShpdGdsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('VatRevAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('DecresGlAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('IncresGlAc')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('Nettable')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StokRvlAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StkOffsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('WipAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('WipVarAcct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('CostRvlAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('CstOffsAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpClrAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ExpOfstAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMFrnAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMEUAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('ARCMExpAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMFrnAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('APCMEUAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('RevRetAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('NegStckAct')
                ->references('id')->on('chart_of_accounts')->nullable();
            $table->integer('StkInTnAct')
                ->references('id')->on('chart_of_accounts')->nullable();

            $table->string('QryGroup1', 1)->default('N');
            $table->string('QryGroup2', 1)->default('N');
            $table->string('QryGroup3', 1)->default('N');
            $table->string('QryGroup4', 1)->default('N');
            $table->string('QryGroup5', 1)->default('N');
            $table->string('QryGroup6', 1)->default('N');
            $table->string('QryGroup7', 1)->default('N');
            $table->string('QryGroup8', 1)->default('N');
            $table->string('QryGroup9', 1)->default('N');
            $table->string('QryGroup10', 1)->default('N');
            $table->string('QryGroup11', 1)->default('N');
            $table->string('QryGroup12', 1)->default('N');
            $table->string('QryGroup13', 1)->default('N');
            $table->string('QryGroup14', 1)->default('N');
            $table->string('QryGroup15', 1)->default('N');
            $table->string('QryGroup16', 1)->default('N');
            $table->string('QryGroup17', 1)->default('N');
            $table->string('QryGroup18', 1)->default('N');
            $table->string('QryGroup19', 1)->default('N');
            $table->string('QryGroup20', 1)->default('N');
            $table->string('QryGroup21', 1)->default('N');
            $table->string('QryGroup22', 1)->default('N');
            $table->string('QryGroup23', 1)->default('N');
            $table->string('QryGroup24', 1)->default('N');
            $table->string('QryGroup25', 1)->default('N');
            $table->string('QryGroup26', 1)->default('N');
            $table->string('QryGroup27', 1)->default('N');
            $table->string('QryGroup28', 1)->default('N');
            $table->string('QryGroup29', 1)->default('N');
            $table->string('QryGroup30', 1)->default('N');
            $table->string('QryGroup31', 1)->default('N');
            $table->string('QryGroup32', 1)->default('N');
            $table->string('QryGroup33', 1)->default('N');
            $table->string('QryGroup34', 1)->default('N');
            $table->string('QryGroup35', 1)->default('N');
            $table->string('QryGroup36', 1)->default('N');
            $table->string('QryGroup37', 1)->default('N');
            $table->string('QryGroup38', 1)->default('N');
            $table->string('QryGroup39', 1)->default('N');
            $table->string('QryGroup40', 1)->default('N');
            $table->string('QryGroup41', 1)->default('N');
            $table->string('QryGroup42', 1)->default('N');
            $table->string('QryGroup43', 1)->default('N');
            $table->string('QryGroup44', 1)->default('N');
            $table->string('QryGroup45', 1)->default('N');
            $table->string('QryGroup46', 1)->default('N');
            $table->string('QryGroup47', 1)->default('N');
            $table->string('QryGroup48', 1)->default('N');
            $table->string('QryGroup49', 1)->default('N');
            $table->string('QryGroup50', 1)->default('N');
            $table->string('QryGroup51', 1)->default('N');
            $table->string('QryGroup52', 1)->default('N');
            $table->string('QryGroup53', 1)->default('N');
            $table->string('QryGroup54', 1)->default('N');
            $table->string('QryGroup55', 1)->default('N');
            $table->string('QryGroup56', 1)->default('N');
            $table->string('QryGroup57', 1)->default('N');
            $table->string('QryGroup58', 1)->default('N');
            $table->string('QryGroup59', 1)->default('N');
            $table->string('QryGroup60', 1)->default('N');
            $table->string('QryGroup61', 1)->default('N');
            $table->string('QryGroup62', 1)->default('N');
            $table->string('QryGroup63', 1)->default('N');
            $table->string('QryGroup64', 1)->default('N');
            $table->string('ExtRef')->nullable(); //External Refrence

            $table->integer('DfltsGroup')->nullable();

            $table->string('CogsOcrCodMthd')->nullable()->comment("Set Dimension,L=Item Level, U=User Level");
            $table->string('CogsOcrCo2Mthd')->nullable()->comment("Set Dimension,L=Item Level, U=User Level");
            $table->string('CogsOcrCo3Mthd')->nullable()->comment("Set Dimension,L=Item Level, U=User Level");
            $table->string('CogsOcrCo4Mthd')->nullable()->comment("Set Dimension,L=Item Level, U=User Level");
            $table->string('CogsOcrCo5Mthd')->nullable()->comment("Set Dimension,L=Item Level, U=User Level");

            $table->string('U_GrpDesc')->nullable(); //U_GrpDesc
            $table->string('U_ProdLine')->nullable(); //U_GrpDesc

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
        Schema::dropIfExists('o_i_t_m_s');
    }
}
