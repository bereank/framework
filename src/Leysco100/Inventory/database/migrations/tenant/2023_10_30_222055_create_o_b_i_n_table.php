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
        Schema::create('o_b_i_n', function (Blueprint $table) {
            $table->id();
            $table->integer('AbsEntry')->nullable();
            $table->string('BinCode', 228)->nullable();
            $table->string('WhsCode', 8)->nullable();
            $table->char('SysBin', 1)->nullable();
            $table->integer('SL1Abs')->nullable();
            $table->string('SL1Code', 50)->nullable();
            $table->integer('SL2Abs')->nullable();
            $table->string('SL2Code', 50)->nullable();
            $table->integer('SL3Abs')->nullable();
            $table->string('SL3Code', 50)->nullable();
            $table->integer('SL4Abs')->nullable();
            $table->string('SL4Code', 50)->nullable();
            $table->integer('Attr1Abs')->nullable();
            $table->string('Attr1Val', 20)->nullable();
            $table->integer('Attr2Abs')->nullable();
            $table->string('Attr2Val', 20)->nullable();
            $table->integer('Attr3Abs')->nullable();
            $table->string('Attr3Val', 20)->nullable();
            $table->integer('Attr4Abs')->nullable();
            $table->string('Attr4Val', 20)->nullable();
            $table->integer('Attr5Abs')->nullable();
            $table->string('Attr5Val', 20)->nullable();
            $table->integer('Attr6Abs')->nullable();
            $table->string('Attr6Val', 20)->nullable();
            $table->integer('Attr7Abs')->nullable();
            $table->string('Attr7Val', 20)->nullable();
            $table->integer('Attr8Abs')->nullable();
            $table->string('Attr8Val', 20)->nullable();
            $table->integer('Attr9Abs')->nullable();
            $table->string('Attr9Val', 20)->nullable();
            $table->integer('Attr10Abs')->nullable();
            $table->string('Attr10Val', 20)->nullable();
            $table->char('Disabled', 1)->nullable();
            $table->string('Descr', 50)->nullable();
            $table->string('BarCode', 100)->nullable();
            $table->string('AltSortCod', 50)->nullable();
            $table->integer('ItmRtrictT')->nullable();
            $table->string('SpcItmCode', 50)->nullable();
            $table->integer('SpcItmGrpC')->nullable();
            $table->char('SngBatch', 1)->nullable();
            $table->integer('RtrictType')->nullable();
            $table->string('RtrictResn', 254)->nullable();
            $table->date('RtrictDate')->nullable();
            $table->char('DataSource', 1)->nullable();
            $table->integer('UserSign')->nullable();
            $table->char('Transfered', 1)->nullable();
            $table->integer('Instance')->nullable();
            $table->integer('LogInstanc')->nullable();
            $table->date('CreateDate')->nullable();
            $table->integer('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->char('Deleted', 1)->nullable();
            $table->float('MinLevel', 19, 6)->nullable();
            $table->float('MaxLevel', 19, 6)->nullable();
            $table->char('ReceiveBin', 1)->nullable();
            $table->char('NoAutoAllc', 1)->nullable();
            $table->float('MaxWeight1', 19, 6)->nullable();
            $table->integer('Wght1Unit')->nullable();
            $table->float('MaxWeight2', 19, 6)->nullable();
            $table->integer('Wght2Unit')->nullable();
            $table->integer('UoMRtrict')->nullable();
            $table->integer('SpcUoMCode')->nullable();
            $table->integer('SpcUGPCode')->nullable();
            $table->integer('SngUoMCode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_i_n');
    }
};
