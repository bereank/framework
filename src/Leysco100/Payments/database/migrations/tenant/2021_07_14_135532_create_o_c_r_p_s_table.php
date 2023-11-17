<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOCRPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_c_r_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->nullable();
            $table->integer('UserSign')->nullable();
            $table->string('OwnerCode')->nullable();
            $table->string('ContactName')->nullable();
            $table->string('DocEntry')->nullable();
            $table->string('DocNum')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('MiddleName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('TransAmount')->nullable();
            $table->dateTime('TransTime')->nullable();
            $table->string('TransID')->nullable();
            $table->string('Balance')->nullable();
            $table->string('Currency')->nullable();
            $table->string('MSISDN')->nullable();
            $table->string('Dscription')->nullable();
            $table->string('TransactType')->nullable();
            $table->string('BusinessKey')->nullable();
            $table->string('BusinessKeyType')->nullable();
            $table->string('debitAccNo')->nullable();
            $table->string('BusinessShortCode')->nullable();
            $table->string('ExtRef')->nullable()->comment("External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("External Refrence Doc Number");
            $table->integer('ReconStatus')->default(0); 
            $table->integer('Source')->nullable();
            $table->string('CompanyID')->nullable();
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
        Schema::dropIfExists('o_c_r_p_s');
    }
}
