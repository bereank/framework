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
        Schema::create('r_i_n19_s', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('DocEntry')->nullable();
            $table->bigInteger('BinAllocSe')->nullable();
            $table->bigInteger('LineNum')->nullable();
            $table->bigInteger('SubLineNum')->nullable();
            $table->bigInteger('SnBType')->nullable();
            $table->bigInteger('SnBMDAbs')->nullable();
            $table->bigInteger('BinAbs')->nullable();
            $table->decimal('Quantity', 19, 6)->nullable();
            $table->string('ItemCode', 50)->nullable();
            $table->string('WhsCode', 8)->nullable();
            $table->string('ObjType', 20)->nullable();
            $table->bigInteger('LogInstanc')->nullable();
            $table->string('AllowNeg', 1)->default('N')->nullable();
            $table->bigInteger('BinActTyp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_i_n19_s');
    }
};
