<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOSCLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_s_c_l_s', function (Blueprint $table) {
            $table->id();
            $table->integer('callID')->nullable();
            $table->string('subject', 190)->nullable();
            $table->string('customer', 15)->nullable();
            $table->string('custmrName', 100)->nullable();
            $table->integer('contctCode')->nullable();
            $table->string('manufSN', 36)->nullable();
            $table->string('internalSN', 36)->nullable();
            $table->integer('contractID')->nullable();
            $table->date('cntrctDate')->nullable();
            $table->date('resolDate')->nullable();
            $table->string('resolTime', 20)->nullable();
            $table->string('free_1', 1)->nullable();
            $table->string('free_2', 1)->nullable();
            $table->string('origin', 1)->nullable();
            $table->string('itemCode', 50)->nullable();
            $table->string('itemName', 100)->nullable();
            $table->integer('itemGroup')->nullable();
            $table->integer('status')->nullable();
            $table->string('priority', 1)->nullable();
            $table->string('callType', 1)->nullable();
            $table->string('problemTyp', 1)->nullable();
            $table->string('assignee', 1)->nullable();
            $table->string('descrption', 16)->nullable();
            $table->string('objType', 1)->nullable();
            $table->integer('logInstanc')->nullable();
            $table->string('userSign', 1)->nullable();
            $table->date('createDate')->nullable();
            $table->string('createTime', 1)->nullable();
            $table->date('closeDate')->nullable();
            $table->string('closeTime', 1)->nullable();
            $table->integer('userSign2')->nullable();
            $table->date('updateDate')->nullable();
            $table->integer('SCL1Count')->default(0);
            $table->integer('SCL2Count')->default(0);
            $table->string('isEntitled', 1)->nullable();
            $table->integer('insID')->nullable();
            $table->integer('technician')->nullable();
            $table->string('resolution', 16)->nullable();
            $table->integer('Scl1NxtLn')->default(0);
            $table->integer('Scl2NxtLn')->default(0);
            $table->integer('Scl3NxtLn')->default(0);
            $table->integer('Scl4NxtLn')->default(0);
            $table->integer('Scl5NxtLn')->default(0);
            $table->string('isQueue', 1)->nullable();
            $table->string('Queue', 1)->nullable();
            $table->string('resolOnDat', 1)->nullable();
            $table->string('resolOnTim', 1)->nullable();
            $table->string('respByDate', 1)->nullable();
            $table->string('respByTime', 1)->nullable();
            $table->string('respOnDate', 1)->nullable();
            $table->string('respOnTime', 1)->nullable();
            $table->string('respAssign', 1)->nullable();
            $table->string('AssignDate', 1)->nullable();
            $table->string('AssignTime', 1)->nullable();
            $table->string('UpdateTime', 1)->nullable();
            $table->string('responder', 1)->nullable();
            $table->string('Transfered', 1)->default('N');
            $table->string('Instance', 1)->nullable();
            $table->integer('DocNum')->nullable();
            $table->string('Series', 1)->nullable();
            $table->string('Handwrtten', 1)->nullable();
            $table->string('PIndicator', 1)->nullable();
            $table->string('StartDate', 1)->nullable();
            $table->string('StartTime', 1)->nullable();
            $table->string('EndDate', 1)->nullable();
            $table->string('EndTime', 1)->nullable();
            $table->float('Duration', 19, 3)->nullable();
            $table->string('DurType', 1)->nullable();
            $table->string('Reminder', 1)->nullable();
            $table->float('RemQty', 19, 2)->default(0);
            $table->string('RemType', 1)->nullable();
            $table->string('RemDate', 1)->nullable();
            $table->string('RemSent', 1)->nullable();
            $table->string('RemTime', 1)->nullable();
            $table->string('Location', 1)->nullable();
            $table->string('AddrName', 1)->nullable();
            $table->string('AddrType', 1)->nullable();
            $table->string('Street', 1)->nullable();
            $table->string('City', 1)->nullable();
            $table->string('Room', 1)->nullable();
            $table->string('State', 1)->nullable();
            $table->string('Country', 1)->nullable();
            $table->string('DisplInCal', 1)->nullable();
            $table->string('SupplCode', 1)->nullable();
            $table->string('Attachment', 1)->nullable();
            $table->string('AtcEntry', 1)->nullable();
            $table->string('NumAtCard', 100)->nullable();
            $table->string('ProSubType', 1)->nullable();
            $table->string('BPType', 1)->nullable();
            $table->string('Telephone', 1)->nullable();
            $table->string('BPPhone1', 1)->nullable();
            $table->string('BPPhone2', 1)->nullable();
            $table->string('BPCellular', 1)->nullable();
            $table->string('BPFax', 1)->nullable();
            $table->string('BPShipCode', 1)->nullable();
            $table->string('BPShipAddr', 1)->nullable();
            $table->string('BPBillCode', 1)->nullable();
            $table->string('BPBillAddr', 1)->nullable();
            $table->string('BPTerrit', 1)->nullable();
            $table->string('BPE_Mail', 1)->nullable();
            $table->string('BPProjCode', 1)->nullable();
            $table->string('BPContact', 1)->nullable();
            $table->integer('OwnerCode')->nullable();
            $table->string('DPPStatus', 1)->nullable();

            $table->string('U_ServiceType')->nullable();
            $table->string('U_LastSrvHrs')->nullable();
            $table->string('U_LtstRunHrs')->nullable();
            $table->string('U_Srvrnd')->nullable();
            $table->integer('U_BAagreement')->nullable();
            $table->string('U_Status', 1)->nullable();
            $table->string('BPLId', 100)->nullable();

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
        Schema::dropIfExists('o_s_c_l_s');
    }
}
