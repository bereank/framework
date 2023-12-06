<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCRGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_r_g_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('GroupCode')->nullable();
            $table->string('GroupName', 20)->nullable();
            $table->string('GroupType', 1)->default('C')->comment("C=Customer Group, S=Vendor Group");
            $table->string('Locked', 1)->default('N');
            $table->string('DataSource', 1)->nullable();
            $table->string('UserSign', 1)->nullable();
            $table->integer('PriceList')
                ->references('id')->on('o_p_l_n_s')->nullable();
            $table->string('DiscRel', 1)->default('L')->comment("//A=Average, H=Highest Discount, L=Lowest Discount, M=Discount Multiples, S=Total");
            $table->string('EffecPrice', 1)->default('D')->comment("//D=Default Priority, H=Highest Price, L=Lowest Price");
            $table->string('ExtRef')->nullable(); //Name
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
        Schema::dropIfExists('o_c_r_g_s');
    }
}
