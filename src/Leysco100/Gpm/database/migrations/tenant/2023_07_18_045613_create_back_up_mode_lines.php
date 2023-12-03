<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_up_mode_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('BaseType');
            $table->string('BaseEntry', 191);
            $table->string('DocNum', 100);
            $table->string('ObjType', 191)->nullable();
            $table->integer('DocOrigin')->default(0); // 1=LS100, 0=SAP
            $table->unsignedBigInteger('DocEntry')->nullable();
            $table->unsignedBigInteger('UserSign')->nullable();
            $table->string('SyncStatus', 191)->default('0'); // 0 not synced, 1 synced
            $table->string('ReleaseStatus', 191)->default('0'); // 0 Pending Release, 1 Released, 2 flagged
            $table->string('Comment', 191)->nullable();
            $table->timestamp('DocDate')->nullable();
            $table->string('OwnerCode', 100)->nullable();
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
        Schema::dropIfExists('back_up_mode_lines');
    }
};
