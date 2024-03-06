<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOUDGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_u_d_g_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Code', 8)->nullable();
            $table->string('Name', 20)->nullable();
            $table->integer('Warehouse')
                ->references('id')->on('o_w_h_s_s')->nullable();
            $table->integer('SalePerson')->nullable();
            $table->integer('DftItmsGrpCod')->nullable();
            $table->integer('Driver')->nullable();
            $table->string('ICTCard', 1)->nullable();
            $table->string('CashAcct', 1)->nullable();
            $table->string('CheckAcct', 1)->nullable();
            $table->string('CreditCard', 1)->nullable();
            $table->string('PrintRcpt', 1)->nullable();
            $table->string('ShortRcpt', 1)->nullable();
            $table->string('Color', 1)->nullable();
            $table->string('Address', 254)->nullable();
            $table->string('Country', 1)->nullable();
            $table->string('PrintHeadr', 1)->nullable();
            $table->string('Phone1', 1)->nullable();
            $table->string('Phone2', 1)->nullable();
            $table->string('Fax', 1)->nullable();
            $table->string('E_Mail', 1)->nullable();
            $table->string('FrgnAddr', 1)->nullable();
            $table->string('FrnPrntHdr', 1)->nullable();
            $table->string('FrgnPhone1', 1)->nullable();
            $table->string('FrgnPhone2', 1)->nullable();
            $table->string('FrgnFax', 1)->nullable();
            $table->string('DflTaxCode', 1)->nullable();
            $table->string('FreeZoneNo', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->string('free1', 1)->nullable();
            $table->string('UseTax', 1)->nullable();
            $table->string('AdrsFromWh', 1)->nullable();
            $table->string('Language', 1)->nullable();
            $table->string('Font', 1)->nullable();
            $table->string('FontSize', 1)->nullable();
            $table->string('BPLId', 150)->nullable();
            $table->string('AssetInDoc', 1)->nullable();
            $table->string('AttachPath', 1)->nullable();
            $table->string('DflPTICode', 1)->nullable();
            $table->string('Free4', 1)->nullable();
            $table->string('Free2', 1)->nullable();
            $table->string('Free3', 1)->nullable();
            $table->string('DflPosCR', 1)->nullable();
            $table->string('TimeFormat', 1)->nullable();
            $table->string('DateFormat', 1)->nullable();
            $table->string('DateSep', 1)->nullable();
            $table->string('DecSep', 1)->nullable();
            $table->string('ThousSep', 1)->nullable();
            $table->string('WallPaper', 1)->nullable();
            $table->string('WllPprDsp', 1)->nullable();
            $table->string('SkinType', 1)->nullable();
            $table->string('CharMonth', 1)->nullable();
            $table->integer('DimCode')->nullable()->comment("Refrences Dimensions");
            $table->string('ExtRef')->nullable()->comment("External Ref");
            $table->string('CogsOcrCod')->nullable()->comment("COGS Distribution Rule Code2");
            $table->string('CogsOcrCo2', 8)->nullable()->comment("COGS Distribution Rule Code2");
            $table->string('CogsOcrCo3', 8)->nullable()->comment("COGS Distribution Rule Code3");
            $table->string('CogsOcrCo4', 8)->nullable()->comment("COGS Distribution Rule Code4");
            $table->string('CogsOcrCo5', 8)->nullable()->comment("COGS Distribution Rule Code5");
            $table->boolean('AddToFavourites')->default(0);
            $table->string('EtstCode', 1)->nullable();
            $table->boolean('ClockIn')->default(0)->nullable();
            $table->boolean('MultiLogin')->default(0)->nullable();
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
        Schema::dropIfExists('o_u_d_g_s');
    }
}
