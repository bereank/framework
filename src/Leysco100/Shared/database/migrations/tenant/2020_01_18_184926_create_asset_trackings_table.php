<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CallCode')
                ->references('id')->on('o_c_l_g_s')->nullable();
            $table->integer('CardCode')
                ->references('id')->on('o_c_d_s');
            $table->integer('UserSign')
                ->references('id')->on('users')->nullable();
            $table->string('AssetName');
            $table->string('Manufacture');
            $table->string('SerialNo')->nullable();
            $table->string('Description')->nullable();
            $table->string('Model')->nullable();
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
        Schema::dropIfExists('asset_trackings');
    }
}
