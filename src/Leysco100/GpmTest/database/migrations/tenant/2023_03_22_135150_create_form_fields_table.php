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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->integer('indexno')->nullable();
            $table->string('title');
            $table->foreignId('type_id')->constrained("form_field_types")->cascadeOnDelete();
            $table->string('mandatory')->comment("Y mandatory, N optional");
            $table->integer('status')->default(1)->comment("1 Active , 0 in-active");
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
        Schema::dropIfExists('form_fields');
    }
};
