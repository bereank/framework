<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('account');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('type')->nullable();
            $table->string('password');
            $table->integer('business_partners')->nullable();
            $table->integer('user_group')
                ->references('id')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('DfltsGroup')->nullable();
            $table->string('SUPERUSER', 1)->default(1);
            $table->integer('all_Branches')->default(0);
            $table->rememberToken();
            $table->integer('Department')->nullable();
            $table->integer('CompanyID')
                ->references('id')->on('companies')->nullable();
            $table->longText('signaturePath')->nullable();
            $table->string('ExtRef')->nullable()->comment("Used For External Refrence");
            $table->integer('EmpID')->nullable()->comment("Employee ID");
            $table->timestamp('active_until')->nullable();
            $table->integer('status')->default(1)->comment("Active: 0=Inactive, 1=Active, 2=Suspended");
            $table->integer('useLocalSearch')->default(0)->comment("0=No, 1=Yes");
            $table->string('localUrl')->nullable();
            $table->integer('gate_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
