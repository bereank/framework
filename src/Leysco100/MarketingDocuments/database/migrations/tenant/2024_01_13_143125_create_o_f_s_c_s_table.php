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
        Schema::create('o_f_s_c_s', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer('ObjType')->default(305)->nullable();
            $table->integer('ObjectCode')->nullable();
            $table->integer('BaseDocEntry')->nullable();
            $table->integer("UserSign")->nullable();    
            $table->string("OwnerCode", 50)->nullable();
            $table->integer('Status')->nullable();
            $table->string('Canceled',50)->nullable();
            $table->integer('LogInst',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_f_s_c_s');
    }
};
