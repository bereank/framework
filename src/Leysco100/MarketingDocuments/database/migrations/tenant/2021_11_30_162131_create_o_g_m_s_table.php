<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOGMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_g_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->default();
            $table->integer('DocEntry')->nullable();
            $table->integer('DocTotal')->nullable();
            $table->string('BaseType')->nullable();
            $table->string('BaseEntry')->nullable();
            $table->string('DocNum', 100)->nullable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->string('ExtRefDocNum')->nullable()->comment("Used For External Refrence Doc Number");
            $table->date('DocDate')->nullable();
            $table->timestamp('GenerationDateTime')->nullable();
            $table->integer('DocOrigin')->default(0)->comment("1=LS100, 0=SAP");
            $table->integer('Status')->default(0)->comment("3=Released,2=Scanned But Flagged,1=Scanned But Not Confirmed, 0=Open");
            $table->longText('LineDetails');
            $table->integer('ScanLogID')->nullable()->comment("Scan Log that realeased the goods");
            $table->string('Comment')->nullable();
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
        Schema::dropIfExists('o_g_m_s');
    }
}
