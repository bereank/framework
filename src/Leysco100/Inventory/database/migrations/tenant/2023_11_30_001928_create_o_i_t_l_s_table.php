<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('o_i_t_l_s', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('LogEntry')->nullable();
            $table->BigInteger('TransId')->nullable();
            $table->string('ItemCode', 50)->nullable();
            $table->string('ItemName', 100)->nullable();
            $table->BigInteger('ManagedBy')->nullable();
            $table->BigInteger('DocEntry')->nullable();
            $table->BigInteger('DocLine')->nullable();
            $table->BigInteger('DocType')->nullable();
            $table->BigInteger('DocNum')->nullable();
            $table->BigInteger('BaseEntry')->nullable();
            $table->BigInteger('BaseLine')->nullable();
            $table->BigInteger('BaseType')->nullable();
            $table->BigInteger('ApplyEntry')->nullable();
            $table->BigInteger('ApplyLine')->nullable();
            $table->BigInteger('ApplyType')->nullable();
            $table->date('DocDate');
            $table->string('CardCode')->nullable();
            $table->string('CardName', 200)->nullable();
            $table->decimal('DocQty', 19, 6)->nullable();
            $table->decimal('StockQty', 19, 6)->nullable();
            $table->decimal('DefinedQty', 19, 6)->nullable();
            $table->BigInteger('StockEff')->nullable();
            $table->date('CreateDate');
            $table->BigInteger('LocType')->nullable();
            $table->string('LocCode', 20)->nullable();
            $table->BigInteger('AppDocNum')->nullable();
            $table->string('VersionNum', 11)->nullable();
            $table->char('Transfered', 1)->nullable();
            $table->BigInteger('Instance')->nullable();
            $table->BigInteger('SubLineNum')->nullable();
            $table->BigInteger('BSubLineNo')->nullable();
            $table->BigInteger('AppSubLine')->nullable();
            $table->BigInteger('ActBaseTp')->nullable();
            $table->BigInteger('ActBaseEnt')->nullable();
            $table->BigInteger('ActBaseLn')->nullable();
            $table->BigInteger('ActBasSubL')->nullable();
            $table->BigInteger('AllocateTp')->nullable();
            $table->BigInteger('AllocatEnt')->nullable();
            $table->BigInteger('AllocateLn')->nullable();
            $table->BigInteger('CreateTime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_i_t_l_s');
    }
};
