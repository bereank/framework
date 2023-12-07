<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOINSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_i_n_s', function (Blueprint $table) {
            $table->id();
            $table->integer('insID')->nullable();
            $table->string('customer', 15)->nullable();
            $table->string('custmrName', 100)->nullable();
            $table->integer('contactCod')->nullable();
            $table->string('directCsmr', 1)->nullable();
            $table->string('drctCsmNam', 1)->nullable();
            $table->string('manufSN')->nullable();
            $table->string('internalSN')->nullable();
            $table->string('warranty')->nullable();
            $table->date('wrrntyStrt')->nullable();
            $table->date('wrrntyEnd')->nullable();
            $table->string('responsVal', 1)->nullable();
            $table->string('responsUnt', 1)->nullable();
            $table->string('itemCode', 50)->nullable();
            $table->string('itemName', 100)->nullable();
            $table->integer('itemGroup')->nullable();
            $table->string('manufDate', 1)->nullable();
            $table->integer('delivery')->nullable();
            $table->integer('deliveryNo')->nullable();
            $table->integer('invoice')->nullable();
            $table->integer('invoiceNum')->nullable();
            $table->date('dlvryDate')->nullable();
            $table->string('cntctPhone', 1)->nullable();
            $table->string('street', 1)->nullable();
            $table->string('block', 1)->nullable();
            $table->string('zip', 1)->nullable();
            $table->string('city', 1)->nullable();
            $table->string('county', 1)->nullable();
            $table->string('country', 1)->nullable();
            $table->string('state', 1)->nullable();
            $table->string('instLction', 1)->nullable();
            $table->string('contract', 1)->nullable();
            $table->string('cntrctStrt', 1)->nullable();
            $table->string('cntrctEnd', 1)->nullable();
            $table->string('attachment', 1)->nullable();
            $table->string('objType', 20)->default(176);
            $table->string('logInstanc', 1)->nullable();
            $table->integer('userSign')->nullable();
            $table->date('createDate')->nullable();
            $table->string('userSign2', 1)->nullable();
            $table->string('updateDate', 1)->nullable();
            $table->string('Building', 1)->nullable();
            $table->string('status', 1)->nullable();
            $table->string('replcIns', 1)->nullable();
            $table->string('repByIns', 1)->nullable();
            $table->integer('technician')->nullable();
            $table->string('territory', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('Transfered', 1)->nullable();
            $table->string('AddrType', 1)->nullable();
            $table->string('Instance', 1)->nullable();
            $table->string('StreetNo', 1)->nullable();
            $table->string('BPType', 1)->nullable();
            $table->string('OwnerCode', 1)->nullable();
            $table->string('DPPStatus', 1)->nullable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
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
        Schema::dropIfExists('o_i_n_s');
    }
}
