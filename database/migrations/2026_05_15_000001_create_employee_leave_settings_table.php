<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLeaveSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_leave_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->decimal('vl_annual_credit', 8, 3)->default(0);
            $table->boolean('vl_is_accumulative')->default(false);
            $table->decimal('sl_annual_credit', 8, 3)->default(0);
            $table->boolean('sl_is_accumulative')->default(false);
            $table->timestamps();

            $table->unique('employee_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_leave_settings');
    }
}
