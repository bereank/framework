<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOIDGSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_i_d_g_s', function (Blueprint $table) {
            $table->id();
            $table->string('Code', 8)->nullable();
            $table->string('Name', 20)->nullable();
            $table->string('CogsOcrCod')->nullable()->comment("COGS Distribution Rule Code2");
            $table->string('CogsOcrCo2', 8)->nullable()->comment("COGS Distribution Rule Code2");
            $table->string('CogsOcrCo3', 8)->nullable()->comment("COGS Distribution Rule Code3");
            $table->string('CogsOcrCo4', 8)->nullable()->comment("COGS Distribution Rule Code4");
            $table->string('CogsOcrCo5', 8)->nullable()->comment("COGS Distribution Rule Code5");
            $table->string('ExtRef')->nullable()->comment("External Ref");
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
        Schema::dropIfExists('o_i_d_g_s');
    }
}
