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
        Schema::create('bcp_auto_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ObjectType')->nullable();
            $table->foreignId('UserSign')->nullable();
            $table->integer('Status')->comment('1=ACTIVE, 0=INACTIVE')->default(1);
            $table->integer('DoesNotExistCount')->nullable();
            $table->integer('LastSyncDuration')->nullable();
            $table->enum('DurationType', ['hours', 'minutes', 'seconds'])->default('minutes');
            $table->foreignId('FieldsTemplate')->default(1);
            $table->boolean('isDistinctDocs')->default(true);
            $table->time('ActiveFrom')->nullable();
            $table->time('ActiveTo')->nullable();
            $table->integer('NotifyAfter')->nullable();
            $table->enum('NotifyType', ['hours', 'minutes'])->default('minutes');
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
        Schema::dropIfExists('bcp_settings');
    }
};
