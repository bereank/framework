<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('PymntGroup')->unique();
            $table->integer('GroupNum')->nullable();
            $table->string('PayDuMonth')->default('N')->nullable();
            $table->string('BslineDate')->default("T");
            $table->integer('ExtraMonth')->default(0)->nullable();
            $table->integer('ExtraDays')->default(0)->nullable();
            $table->string('NumOfPmnts')->default(1)->nullable();
            $table->string('OpenRcpt')->default('N')->nullable();
            $table->string('LatePyChrg')->default(0)->nullable();
            $table->string('VolumDscnt')->default(0)->nullable();
            $table->integer('InstNum')->default(0)->nullable();
            ;
            $table->integer('ListNum')
                ->references('id')->on('price_lists')->nullable();
            $table->integer('DiscCode')
                ->references('id')->on('price_lists')->nullable();
            $table->string('CredLimit')->default(0)->nullable();
            $table->string('ObligLimit')->default(0)->nullable();
            $table->softDeletes();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
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
        Schema::dropIfExists('payment_terms');
    }
}
