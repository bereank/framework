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
        Schema::create('o_a_l_t', function (Blueprint $table) {
            $table->id();
            $table->string('Code')->nullable();
            $table->string('Name')->nullable();
            $table->string('Type')->nullable();
            $table->integer('Priority')->nullable();
            $table->boolean('Active')->default(true);
            $table->integer('NumOfParam')->nullable();
            $table->text('ParamData')->nullable();
            $table->text('Params')->nullable();
            $table->integer('NumOfDocs')->nullable();
            $table->text('DocsData')->nullable();
            $table->text('Docs')->nullable();
            $table->text('UserText')->nullable();
            $table->integer('QueryId')->nullable();
            $table->string('FrqncyType')->nullable();
            $table->string('FrqncyIntr')->nullable();
            $table->string('ExecDaY')->nullable();
            $table->string('ExecTime')->nullable();
            $table->date('LastDate')->nullable();
            $table->time('LastTIME')->nullable();
            $table->date('NextDate')->nullable();
            $table->time('NextTime')->nullable();
            $table->string('UserSign')->nullable();
            $table->text('History')->nullable();
            $table->string('QCategory')->nullable();
            $table->boolean('Retry')->default(0);
            $table->integer('RetryAfter')->nullable();
            $table->time('MaxRetryTime')->nullable();
            $table->time('RetryTime')->nullable();
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
        Schema::dropIfExists('o_a_l_t');
    }
};
