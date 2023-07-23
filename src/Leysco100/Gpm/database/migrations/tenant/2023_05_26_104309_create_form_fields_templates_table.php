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
        Schema::create('form_fields_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ObjectType')->nullable();
            $table->foreignId('UserSign');
            $table->string('Name');
            $table->integer('DefaultTemplate')->nullable()->unique();
            $table->integer('Enabled')->default(0)->comment("1 Enabled , 0 inactive")->nullable();
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
        Schema::dropIfExists('form_fields_templates');
    }
};
