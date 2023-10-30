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
        Schema::create('o_b_s_l', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('AbsEntry')->nullable();
            $table->bigInteger('FldAbs')->nullable();
            $table->string('SLCode', 50)->nullable();
            $table->string('Descr', 50)->nullable();
            $table->bigInteger('UserSign')->nullable();
            $table->char('DataSource', 1)->nullable();
            $table->char('Transfered', 1)->nullable();
            $table->bigInteger('Instance')->nullable();
            $table->bigInteger('LogInstanc')->nullable();
            $table->date('CreateDate')->nullable();
            $table->bigInteger('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->char('Deleted', 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_s_l');
    }
};
