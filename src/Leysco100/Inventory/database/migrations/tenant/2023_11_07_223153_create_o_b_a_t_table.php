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
        Schema::create('o_b_a_t', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('AbsEntry')->nullable();
            $table->bigInteger('FldAbs')->nullable();
            $table->string('AttrValue', 20)->nullable();
            $table->char('DataSource', 1)->nullable();
            $table->bigInteger('UserSign')->nullable();
            $table->boolean('Transfered')->default(0);
            $table->bigInteger('Instance')->nullable();
            $table->bigInteger('LogInstanc')->nullable();
            $table->date('CreateDate')->nullable();
            $table->bigInteger('UserSign2')->nullable();
            $table->date('UpdateDate')->nullable();
            $table->boolean('Deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_b_a_t');
    }
};
