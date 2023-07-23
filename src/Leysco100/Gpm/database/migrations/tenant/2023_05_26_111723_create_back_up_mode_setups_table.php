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
        Schema::create('back_up_mode_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ObjectType')->nullable();
            $table->foreignId('UserSign')->nullable();
            $table->integer('Enabled')->default(true);
            $table->integer('Type')->default(1)->comment('1=Company wide, 2=Gates, 3 Users');
            $table->date('StartDate')->nullable();
            $table->time('StartTime')->nullable();
            $table->unsignedSmallInteger('Hours')->nullable();
            $table->unsignedSmallInteger('Minutes')->nullable();
            $table->timestamp('EndTime')->nullable();
            $table->integer('OwnerID');
            $table->integer('activatable_id')->nullable();
            $table->string('activatable_type')->nullable();
            $table->foreignId('FieldsTemplate')->nullable();
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
        Schema::dropIfExists('back_up_mode_setups');
    }
};
