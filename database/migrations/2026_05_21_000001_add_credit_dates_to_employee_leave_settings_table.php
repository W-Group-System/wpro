<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditDatesToEmployeeLeaveSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('employee_leave_settings', function (Blueprint $table) {
            // "Renew every year" fields: specific month+day the credit resets
            $table->tinyInteger('vl_credit_month')->nullable()->after('vl_is_accumulative');
            $table->tinyInteger('vl_credit_day')->nullable()->after('vl_credit_month');
            // "Accumulative" field: day of month credits accumulate
            $table->tinyInteger('vl_accumulate_day')->nullable()->after('vl_credit_day');

            $table->tinyInteger('sl_credit_month')->nullable()->after('sl_is_accumulative');
            $table->tinyInteger('sl_credit_day')->nullable()->after('sl_credit_month');
            $table->tinyInteger('sl_accumulate_day')->nullable()->after('sl_credit_day');
        });
    }

    public function down()
    {
        Schema::table('employee_leave_settings', function (Blueprint $table) {
            $table->dropColumn([
                'vl_credit_month', 'vl_credit_day', 'vl_accumulate_day',
                'sl_credit_month', 'sl_credit_day', 'sl_accumulate_day',
            ]);
        });
    }
}
