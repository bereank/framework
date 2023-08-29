<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWTM4STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_t_m4_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('WtmCode')->nullable();
            $table->integer('CondId')->nullable(); //    0=Undefined Type, 10=Counted Quantity, 11=Variance, 12=Variance %, 1=Deviation from Credit Limit, 2=Deviation from Commitment, 3=Gross Profit %, 4=Discount %, 5=Deviation from Budget, 6=Total Document, 7=Quantity, 8=Item Code, 9=Total
            $table->integer('opCode')->nullable(); //0=Undefined Type, 1=Greater Than, 2=Greater or Equal, 3=Less Than, 4=Less or Equal, 5=Equal, 6=Does not Equal, 7=In Range, 8=Not in Range
            $table->string('opValue', 100)->nullable();
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
        Schema::dropIfExists('w_t_m4_s');
    }
}
